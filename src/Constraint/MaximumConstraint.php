<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal\Constraint;

use JVal\Context;
use JVal\Walker;
use stdClass;

/**
 * Constraint for the "maximum" and "exclusiveMaximum" keywords.
 */
class MaximumConstraint extends AbstractRangeConstraint
{
    /**
     * {@inheritDoc}
     */
    public function keywords()
    {
        return ['maximum', 'exclusiveMaximum'];
    }

    /**
     * {@inheritDoc}
     */
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
