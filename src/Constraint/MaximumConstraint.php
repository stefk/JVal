<?php

namespace JsonSchema\Constraint;

use JsonSchema\ConstraintInterface;
use JsonSchema\Context;
use JsonSchema\Registry;
use JsonSchema\Walker;
use stdClass;

class MaximumConstraint implements ConstraintInterface
{
    public function keywords()
    {
        return ['maximum', 'exclusiveMaximum'];
    }

    public function isApplicableTo($type)
    {
        return $type === Registry::TYPE_INTEGER
            || $type === Registry::TYPE_NUMBER;
    }

    public function normalize(stdClass $schema)
    {

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
