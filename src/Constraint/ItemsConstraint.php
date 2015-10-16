<?php

namespace JsonSchema\Constraint;

use JsonSchema\ConstraintInterface;
use JsonSchema\Context;
use JsonSchema\Registry;
use JsonSchema\Walker;
use stdClass;

class ItemsConstraint implements ConstraintInterface
{
    public function keywords()
    {
        return ['items', 'additionalItems'];
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
        if (isset($schema->items)
            && is_array($schema->items)
            && isset($schema->additionalItems) 
            && $schema->additionalItems === false 
            && count($instance) > count($schema->items)) {
            $context->addViolation('additional items are not allowed');
        }
    }
}
