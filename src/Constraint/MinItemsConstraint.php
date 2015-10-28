<?php

namespace JVal\Constraint;

use JVal\Context;
use JVal\Types;
use JVal\Walker;
use stdClass;

/**
 * Constraint for the "minItems" keyword.
 */
class MinItemsConstraint extends AbstractCountConstraint
{
    /**
     * {@inheritDoc}
     */
    public function keywords()
    {
        return ['minItems'];
    }

    /**
     * {@inheritDoc}
     */
    public function supports($type)
    {
        return $type === Types::TYPE_ARRAY;
    }

    /**
     * {@inheritDoc}
     */
    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        if (count($instance) < $schema->minItems) {
            $context->addViolation(
                'number of items should be greater than or equal to %s',
                [$schema->minItems]
            );
        }
    }
}
