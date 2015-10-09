<?php

namespace JsonSchema\Constraint;

use JsonSchema\ConstraintInterface;
use JsonSchema\Context;
use stdClass;

class ItemsConstraint implements ConstraintInterface
{
    public function keywords()
    {
        return ['items', 'additionalItems'];
    }

    public function isApplicableTo($instance)
    {
        return is_array($instance);
    }

    public function apply($instance, stdClass $schema, Context $context)
    {
        if (isset($schema->items)
            && is_array($schema->items)
            && isset($schema->additionalItems) 
            && $schema->additionalItems === false 
            && count($instance) > count($schema->items)) {
            $context->addViolation('additional items are not allowed');
        }
    }
}
