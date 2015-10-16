<?php

namespace JsonSchema\Constraint;

use JsonSchema\ConstraintInterface;
use JsonSchema\Context;
use JsonSchema\Registry;
use JsonSchema\Walker;
use stdClass;

class MaxItemsConstraint implements ConstraintInterface
{
    public function keywords()
    {
        return ['maxItems'];
    }

    public function isApplicableTo($type)
    {
        return $type === Registry::TYPE_ARRAY;
    }

    public function normalize(stdClass $schema)
    {

    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        if (count($instance) > $schema->maxItems) {
            $context->addViolation(
                'number of items should be less than, or equal to, %s',
                [$schema->maxItems]
            );
        }
    }
}
