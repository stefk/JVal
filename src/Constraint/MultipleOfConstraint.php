<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
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

    public function normalize(stdClass $schema)
    {
        if (!Types::isA($schema->multipleOf, Types::TYPE_NUMBER)) {
            throw new ConstraintException(
                'multipleOf must be a number',
                ConstraintException::MULTIPLE_OF_NOT_NUMBER
            );
        }

        if ($schema->multipleOf <= 0) {
            throw new ConstraintException(
                'multipleOf must be greater than 0',
                ConstraintException::MULTIPLE_OF_NOT_POSITIVE
            );
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
