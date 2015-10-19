<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\Constraint\MultipleOfNotNumberException;
use JsonSchema\Exception\Constraint\MultipleOfNotPositiveException;
use JsonSchema\Exception\ConstraintException;
use JsonSchema\Types;
use JsonSchema\Walker;
use stdClass;

class MultipleOfConstraint implements Constraint
{
    public function keywords()
    {
        return ['multipleOf'];
    }

    public function supports($type)
    {
        return $type === Types::TYPE_INTEGER || $type === Types::TYPE_NUMBER;
    }

    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        if (!Types::isA($schema->multipleOf, Types::TYPE_NUMBER)) {
            throw new MultipleOfNotNumberException($context);
        }

        if ($schema->multipleOf <= 0) {
            throw new MultipleOfNotPositiveException($context);
        }
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
            $decimals1 = mb_strpos($instance, '.') ?
                mb_strlen($instance) - mb_strpos($instance, '.') - 1 :
                0;
            $decimals2 = mb_strpos($divider, '.') ?
                mb_strlen($divider) - mb_strpos($divider, '.') - 1 :
                0;
            $fMod = (float) round($modulus, max($decimals1, $decimals2));
        }

        if ($fMod != 0) {
            $context->addViolation('should be a multiple of %s', [$divider]);
        }
    }
}
