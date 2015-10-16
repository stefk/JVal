<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Registry;
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
        return $type === Registry::TYPE_INTEGER
            || $type === Registry::TYPE_NUMBER;
    }

    public function normalize(stdClass $schema)
    {
        // if max not set, throw (if exMax set, max must be present)
        // if exMax not set, exclusiveMax = false

        // if maximum != number, throw
        // if exclusiveMaximum != bool, throw
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
