<?php

namespace JsonSchema\Constraint;

use JsonSchema\Context;
use JsonSchema\Walker;
use stdClass;

class MinimumConstraint extends AbstractRangeConstraint
{
    public function keywords()
    {
        return ['minimum', 'exclusiveMinimum'];
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        if ($schema->exclusiveMinimum === false) {
            if ($instance < $schema->minimum) {
                $context->addViolation('should be greater than or equal to %s', [$schema->minimum]);
            }
        } elseif ($instance <= $schema->minimum) {
            $context->addViolation('should be greater than %s', [$schema->minimum]);
        }
    }
}
