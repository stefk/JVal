<?php

namespace JsonSchema\Constraint;

use JsonSchema\ConstraintInterface;
use JsonSchema\Context;
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

    public function apply($instance, stdClass $schema, Context $context)
    {
        if (!isset($schema->exclusiveMaximum) || $schema->exclusiveMaximum === false) {
            if ($instance > $schema->maximum) {
                $context->addViolation('err');
            }
        }

        // if schema->exclusiveMaximum == true and instance >= maximum, add error
    }
}
