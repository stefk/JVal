<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\Constraint\TypeElementNotStringException;
use JsonSchema\Exception\Constraint\TypeElementNotUniqueException;
use JsonSchema\Exception\Constraint\TypeInvalidTypeException;
use JsonSchema\Exception\Constraint\TypeNotPrimitiveTypeException;
use JsonSchema\Types;
use JsonSchema\Walker;
use stdClass;

class TypeConstraint implements Constraint
{
    public function keywords()
    {
        return ['type'];
    }

    public function supports($type)
    {
        return true;
    }

    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        if (is_string($schema->type)) {
            if (!Types::isPrimitive($schema->type)) {
                throw new TypeNotPrimitiveTypeException($context, [$schema->type]);
            }
        } else if (is_array($schema->type)) {
            foreach ($schema->type as $index => $type) {
                if (!is_string($type)) {
                    throw new TypeElementNotStringException($context, [$index]);
                }

                if (!Types::isPrimitive($type)) {
                    throw new TypeNotPrimitiveTypeException($context, [$type]);
                }
            }

            if (count(array_unique($schema->type)) !== count($schema->type)) {
                throw new TypeElementNotUniqueException($context);
            }
        } else {
            throw new TypeInvalidTypeException($context);
        }
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        if (is_string($schema->type)) {
            if (!Types::isA($instance, $schema->type)) {
                $context->addViolation('instance must be of type %s', [$schema->type]);
            }
        } else {
            $hasMatch = false;

            foreach ($schema->type as $type) {
                if (Types::isA($instance, $type)) {
                    $hasMatch = true;
                    break;
                }
            }

            if (!$hasMatch) {
                $context->addViolation('instance does not match any of the expected types');
            }
        }
    }
}
