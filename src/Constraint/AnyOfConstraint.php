<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\Constraint\EmptyArrayException;
use JsonSchema\Exception\Constraint\InvalidTypeException;
use JsonSchema\Types;
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
        $context->enterNode($schema->anyOf, 'anyOf');

        if (!is_array($schema->anyOf)) {
            throw new InvalidTypeException($context, Types::TYPE_ARRAY);
        }

        if (count($schema->anyOf) === 0) {
            throw new EmptyArrayException($context);
        }

        foreach ($schema->anyOf as $index => $subSchema) {
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
