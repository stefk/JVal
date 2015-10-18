<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;
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
        if (!is_int($schema->maxProperties)) {
            throw new ConstraintException(
                'maxProperties must be an integer',
                ConstraintException::MAX_PROPERTIES_NOT_INTEGER,
                $context
            );
        }

        if ($schema->maxProperties <= 0) {
            throw new ConstraintException(
                'maxProperties must be greater than 0',
                ConstraintException::MAX_PROPERTIES_NOT_POSITIVE,
                $context
            );
        }
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
