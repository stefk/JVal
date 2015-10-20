<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\Constraint\InvalidTypeException;
use JsonSchema\Exception\Constraint\LessThanZeroException;
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

    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        $context->enterNode($schema->maxProperties, 'maxProperties');

        if (!is_int($schema->maxProperties)) {
            throw new InvalidTypeException($context, Types::TYPE_INTEGER);
        }

        if ($schema->maxProperties <= 0) {
            throw new LessThanZeroException($context);
        }

        $context->leaveNode();
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
