<?php

namespace JsonSchema\Constraint;

use JsonSchema\Context;
use JsonSchema\Types;
use JsonSchema\Walker;
use stdClass;

class MaxLengthConstraint extends AbstractCountConstraint
{
    public function keywords()
    {
        return ['maxLength'];
    }

    public function supports($type)
    {
        return $type === Types::TYPE_STRING;
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        $length = extension_loaded('mbstring') ?
            mb_strlen($instance, mb_detect_encoding($instance)) :
            strlen($instance);

        if ($length > $schema->maxLength) {
            $context->addViolation(
                'should be lesser than or equal to %s characters',
                [$schema->maxLength]
            );
        }
    }
}
