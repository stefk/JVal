<?php

namespace JVal\Constraint;

use JVal\Context;
use JVal\Types;
use JVal\Walker;
use stdClass;

/**
 * Constraint for the "minLength" keyword.
 */
class MinLengthConstraint extends AbstractCountConstraint
{
    /**
     * {@inheritDoc}
     */
    public function keywords()
    {
        return ['minLength'];
    }

    /**
     * {@inheritDoc}
     */
    public function supports($type)
    {
        return $type === Types::TYPE_STRING;
    }

    /**
     * {@inheritDoc}
     */
    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        $length = extension_loaded('mbstring') ?
            mb_strlen($instance, mb_detect_encoding($instance)) :
            strlen($instance);

        if ($length < $schema->minLength) {
            $context->addViolation(
                'should be greater than or equal to %s characters',
                [$schema->minLength]
            );
        }
    }
}
