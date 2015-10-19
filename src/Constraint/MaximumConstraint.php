<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\Constraint\ExclusiveMaximumNotBooleanException;
use JsonSchema\Exception\Constraint\MaximumNotNumberException;
use JsonSchema\Exception\Constraint\MaximumNotPresentException;
use JsonSchema\Types;
use JsonSchema\Walker;
use stdClass;

class MaximumConstraint implements Constraint
{
    public function keywords()
    {
        return ['maximum', 'exclusiveMaximum'];
    }

    public function supports($type)
    {
        return $type === Types::TYPE_INTEGER
            || $type === Types::TYPE_NUMBER;
    }

    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        if (!isset($schema->maximum)) {
            throw new MaximumNotPresentException($context);
        }

        if (!isset($schema->exclusiveMaximum)) {
            $schema->exclusiveMaximum = false;
        }

        if (!Types::isA($schema->maximum, Types::TYPE_NUMBER)) {
            throw new MaximumNotNumberException($context);
        }

        if (!is_bool($schema->exclusiveMaximum)) {
            throw new ExclusiveMaximumNotBooleanException($context);
        }
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        if (!isset($schema->exclusiveMaximum) || $schema->exclusiveMaximum === false) {
            if ($instance > $schema->maximum) {
                $context->addViolation('should be less than, or equal to, %s', [$schema->maximum]);
            }
        } else if ($instance >= $schema->maximum) {
            $context->addViolation('should be less than %s', [$schema->maximum]);
        }
    }
}
