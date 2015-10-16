<?php

namespace JsonSchema\Constraint;

use JsonSchema\ConstraintInterface;
use JsonSchema\Context;
use JsonSchema\Registry;
use JsonSchema\Walker;
use stdClass;

class MaxPropertiesConstraint implements ConstraintInterface
{
    public function keywords()
    {
        return ['maxProperties'];
    }

    public function isApplicableTo($type)
    {
        return $type === Registry::TYPE_OBJECT;
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
