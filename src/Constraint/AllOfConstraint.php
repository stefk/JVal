<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\Constraint\EmptyArrayException;
use JsonSchema\Exception\Constraint\InvalidTypeException;
use JsonSchema\Types;
use JsonSchema\Walker;
use stdClass;

class AllOfConstraint implements Constraint
{
    public function keywords()
    {
        return ['allOf'];
    }

    public function supports($type)
    {
        return true;
    }

    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        $context->enterNode($schema->allOf, 'allOf');

        if (!is_array($schema->allOf)) {
            throw new InvalidTypeException($context, Types::TYPE_ARRAY);
        }

        if (count($schema->allOf) === 0) {
            throw new EmptyArrayException($context);
        }

        foreach ($schema->allOf as $index => $subSchema) {
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

        foreach ($schema->allOf as $subSchema) {
            $walker->applyConstraints($instance, $subSchema, $context);
        }

        if (count($context->getViolations()) > $violationCount) {
            $context->addViolation('instance must match all the schemas listed in allOf');
        }
    }
}
