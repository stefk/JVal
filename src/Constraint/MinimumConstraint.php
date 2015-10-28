<?php

namespace JVal\Constraint;

use JVal\Context;
use JVal\Walker;
use stdClass;

/**
 * Constraint for the "minimum" and "exclusiveMinimum" keywords.
 */
class MinimumConstraint extends AbstractRangeConstraint
{
    /**
     * {@inheritDoc}
     */
    public function keywords()
    {
        return ['minimum', 'exclusiveMinimum'];
    }

    /**
     * {@inheritDoc}
     */
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
