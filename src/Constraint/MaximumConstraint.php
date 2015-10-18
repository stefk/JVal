<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;
use JsonSchema\Types;
use JsonSchema\Walker;
use stdClass;

class MaximumConstraint implements Constraint
{
    public function keywords()
    {
        return ['maximum', 'exclusiveMaximum'];
    }

    public function supports($type)
    {
        return $type === Types::TYPE_INTEGER
            || $type === Types::TYPE_NUMBER;
    }

    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        if (!isset($schema->maximum)) {
            throw new ConstraintException(
                "maximum keyword must be present",
                ConstraintException::MAXIMUM_NOT_PRESENT
            );
        }

        if (!isset($schema->exclusiveMaximum)) {
            $schema->exclusiveMaximum = false;
        }

        if (!Types::isA($schema->maximum, Types::TYPE_NUMBER)) {
            throw new ConstraintException(
                'maximum must be a number',
                ConstraintException::MAXIMUM_NOT_NUMBER
            );
        }

        if (!is_bool($schema->exclusiveMaximum)) {
            throw new ConstraintException(
                'exclusiveMaximum must be a boolean',
                ConstraintException::EXCLUSIVE_MAXIMUM_NOT_BOOLEAN
            );
        }
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        if (!isset($schema->exclusiveMaximum) || $schema->exclusiveMaximum === false) {
            if ($instance > $schema->maximum) {
                $context->addViolation('should be less than, or equal to, %s', [$schema->maximum]);
            }
        } else if ($instance >= $schema->maximum) {
            $context->addViolation('should be less than %s', [$schema->maximum]);
        }
    }
}
