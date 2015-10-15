<?php

namespace JsonSchema\Constraint;

use JsonSchema\ConstraintInterface;
use JsonSchema\Context;
use JsonSchema\Walker;
use stdClass;

class MaximumConstraint implements ConstraintInterface
{
    public function keywords()
    {
        return ['maximum', 'exclusiveMaximum'];
    }

    public function isApplicableTo($instance)
    {
        return !is_string($instance) && is_numeric($instance);
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
