<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\Constraint\InvalidTypeException;
use JsonSchema\Exception\Constraint\LessThanZeroException;
use JsonSchema\Types;
use JsonSchema\Walker;
use stdClass;

class MaxItemsConstraint implements Constraint
{
    public function keywords()
    {
        return ['maxItems'];
    }

    public function supports($type)
    {
        return $type === Types::TYPE_ARRAY;
    }

    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        $context->enterNode($schema->maxItems, 'maxItems');

        if (!is_int($schema->maxItems)) {
            throw new InvalidTypeException($context, Types::TYPE_INTEGER);
        }

        if ($schema->maxItems < 0) {
            throw new LessThanZeroException($context);
        }

        $context->leaveNode();
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
