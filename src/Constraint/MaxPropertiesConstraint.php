<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Types;
use JsonSchema\Walker;
use stdClass;

class MaxPropertiesConstraint implements Constraint
{
    public function keywords()
    {
        return ['maxProperties'];
    }

    public function supports($type)
    {
        return $type === Types::TYPE_OBJECT;
    }

    public function normalize(stdClass $schema)
    {

    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        if (count(get_object_vars($instance)) > $schema->maxProperties) {
            $context->addViolation(
                'number of properties should be less than, or equal to, %s',
                [$schema->maxProperties]
            );
        }
    }
}
