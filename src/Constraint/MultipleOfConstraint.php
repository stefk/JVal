<?php

namespace JsonSchema\Constraint;

use JsonSchema\ConstraintInterface;
use JsonSchema\Context;
use JsonSchema\Walker;
use stdClass;

class MultipleOfConstraint implements ConstraintInterface
{
    public function keywords()
    {
        return ['multipleOf'];
    }

    public function isApplicableTo($instance)
    {
        return !is_string($instance) && is_numeric($instance);
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        $divider = $schema->multipleOf;
        $modulus = fmod($instance, $divider);
        $precision = abs(0.0000000001);
        $diff = (float)($modulus - $divider);

        if (-$precision < $diff && $diff < $precision) {
            $fMod = 0.0;
        } else {
            $decimals1 = mb_strpos($instance, '.') ? mb_strlen($instance) - mb_strpos($instance, '.') - 1 : 0;
            $decimals2 = mb_strpos($divider, '.') ? mb_strlen($divider) - mb_strpos($divider, '.') - 1 : 0;
            $fMod = (float) round($modulus, max($decimals1, $decimals2));
        }

        if ($fMod != 0) {
            $context->addViolation('should be a multiple of %s', [$divider]);
        }
    }
}
