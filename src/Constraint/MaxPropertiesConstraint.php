<?php

namespace JVal\Constraint;

use JVal\Context;
use JVal\Types;
use JVal\Walker;
use stdClass;

class MaxPropertiesConstraint extends AbstractCountConstraint
{
    public function keywords()
    {
        return ['maxProperties'];
    }

    public function supports($type)
    {
        return $type === Types::TYPE_OBJECT;
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        if (count(get_object_vars($instance)) > $schema->maxProperties) {
            $context->addViolation(
                'number of properties should be lesser than or equal to %s',
                [$schema->maxProperties]
            );
        }
    }
}
