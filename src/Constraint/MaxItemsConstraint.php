<?php

namespace JsonSchema\Constraint;

use JsonSchema\ConstraintInterface;
use JsonSchema\Context;
use stdClass;

class MaxItemsConstraint implements ConstraintInterface
{
    public function keywords()
    {
        return ['maxItems'];
    }

    public function isApplicableTo($instance)
    {
        return is_array($instance);
    }

    public function apply($instance, stdClass $schema, Context $context)
    {
        if (count($instance) > $schema->maxItems) {
            $context->addViolation(
                'number of items should be less than, or equal to, %s',
                [$schema->maxItems]
            );
        }
    }
}
