<?php

namespace JsonSchema\Constraint;

use JsonSchema\Context;
use JsonSchema\Types;
use JsonSchema\Walker;
use stdClass;

class MinItemsConstraint extends AbstractCountConstraint
{
    public function keywords()
    {
        return ['minItems'];
    }

    public function supports($type)
    {
        return $type === Types::TYPE_ARRAY;
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        if (count($instance) < $schema->minItems) {
            $context->addViolation(
                'number of items should be greater than or equal to %s',
                [$schema->minItems]
            );
        }
    }
}
