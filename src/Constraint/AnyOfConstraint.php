<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\Constraint\AnyOfElementNotObjectException;
use JsonSchema\Exception\Constraint\AnyOfEmptyException;
use JsonSchema\Exception\Constraint\AnyOfNotArrayException;
use JsonSchema\Walker;
use stdClass;

class AnyOfConstraint implements Constraint
{
    public function keywords()
    {
        return ['anyOf'];
    }

    public function supports($type)
    {
        return true;
    }

    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        $startPath = $context->getCurrentPath();

        if (!is_array($schema->anyOf)) {
            throw new AnyOfNotArrayException($context);
        }

        if (count($schema->anyOf) === 0) {
            throw new AnyOfEmptyException($context);
        }

        foreach ($schema->anyOf as $index => $subSchema) {
            $context->setNode($subSchema, "{$startPath}/{$index}");

            if (!is_object($subSchema)) {
                throw new AnyOfElementNotObjectException($context, [$index]);
            }

            $walker->parseSchema($subSchema, $context);
        }

        $context->setNode($schema, $startPath);
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        $accumulatingContext = $context->duplicate();
        $hasMatch = false;

        foreach ($schema->anyOf as $subSchema) {
            $violationCount = count($accumulatingContext->getViolations());
            $walker->applyConstraints($instance, $subSchema, $accumulatingContext);

            if (count($accumulatingContext->getViolations()) === $violationCount) {
                $hasMatch = true;
                break;
            }
        }

        if (!$hasMatch) {
            $context->mergeViolations($accumulatingContext);
            $context->addViolation('instance must match at least one of the schemas listed in anyOf');
        }
    }
}
