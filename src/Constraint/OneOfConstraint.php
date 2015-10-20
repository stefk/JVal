<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\Constraint\EmptyArrayException;
use JsonSchema\Exception\Constraint\InvalidTypeException;
use JsonSchema\Types;
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
        $context->enterNode($schema->oneOf, 'oneOf');

        if (!is_array($schema->oneOf)) {
            throw new InvalidTypeException($context, Types::TYPE_ARRAY);
        }

        if (count($schema->oneOf) === 0) {
            throw new EmptyArrayException($context);
        }

        foreach ($schema->oneOf as $index => $subSchema) {
            $context->enterNode($subSchema, $index + 1);

            if (!is_object($subSchema)) {
                throw new InvalidTypeException($context, Types::TYPE_OBJECT);
            }

            $walker->parseSchema($subSchema, $context);
            $context->leaveNode();
        }

        $context->leaveNode();
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
