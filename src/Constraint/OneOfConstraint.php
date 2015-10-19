<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\Constraint\OneOfElementNotObjectException;
use JsonSchema\Exception\Constraint\OneOfEmptyException;
use JsonSchema\Exception\Constraint\OneOfNotArrayException;
use JsonSchema\Walker;
use stdClass;

class OneOfConstraint implements Constraint
{
    public function keywords()
    {
        return ['oneOf'];
    }

    public function supports($type)
    {
        return true;
    }

    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        $startPath = $context->getCurrentPath();

        if (!is_array($schema->oneOf)) {
            throw new OneOfNotArrayException($context);
        }

        if (count($schema->oneOf) === 0) {
            throw new OneOfEmptyException($context);
        }

        foreach ($schema->oneOf as $index => $subSchema) {
            $context->setNode($subSchema, "{$startPath}/{$index}");

            if (!is_object($subSchema)) {
                throw new OneOfElementNotObjectException($context, [$index]);
            }

            $walker->parseSchema($subSchema, $context);
        }

        $context->setNode($schema, $startPath);
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        $violationCount = count($context->getViolations());
        $hasMatch = false;
        $hasDoubleMatch = false;

        foreach ($schema->oneOf as $subSchema) {
            $subContext = $context->duplicate();
            $walker->applyConstraints($instance, $subSchema, $subContext);

            if (count($subContext->getViolations()) === $violationCount) {
                if (!$hasMatch) {
                    $hasMatch = true;
                } else {
                    $hasDoubleMatch = true;
                    break;
                }
            }
        }

        if (!$hasMatch || $hasDoubleMatch) {
            $context->addViolation('instance must match exactly one of the schemas listed in oneOf');
        }
    }
}
