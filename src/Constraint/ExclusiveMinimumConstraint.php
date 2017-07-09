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
 * Constraint for the "exclusiveMinimum" keyword.
 */
class ExclusiveMinimumConstraint extends AbstractRangeConstraint
{
    /**
     * {@inheritdoc}
     */
    public function keywords()
    {
        return ['exclusiveMinimum'];
    }

    /**
     * {@inheritdoc}
     */
    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        if ($instance <= $schema->exclusiveMinimum) {
            $context->addViolation('should be greater than %s', [$schema->exclusiveMinimum]);
        }
    }
}
