<?php

namespace JVal\Constraint;

use JVal\Context;
use JVal\Types;
use JVal\Walker;
use stdClass;

class MinPropertiesConstraint extends AbstractCountConstraint
{
    public function keywords()
    {
        return ['minProperties'];
    }

    public function supports($type)
    {
        return $type === Types::TYPE_OBJECT;
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        if (count(get_object_vars($instance)) < $schema->minProperties) {
            $context->addViolation(
                'number of properties should be greater than or equal to %s',
                [$schema->minProperties]
            );
        }
    }
}
