<?php

namespace JsonSchema\Constraint;

use JsonSchema\Context;
use JsonSchema\Walker;
use stdClass;

class MaximumConstraint extends AbstractRangeConstraint
{
    public function keywords()
    {
        return ['maximum', 'exclusiveMaximum'];
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        if ($schema->exclusiveMaximum === false) {
            if ($instance > $schema->maximum) {
                $context->addViolation('should be lesser than or equal to %s', [$schema->maximum]);
            }
        } elseif ($instance >= $schema->maximum) {
            $context->addViolation('should be lesser than %s', [$schema->maximum]);
        }
    }
}
