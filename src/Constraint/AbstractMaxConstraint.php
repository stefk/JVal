<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\Constraint\InvalidTypeException;
use JsonSchema\Exception\Constraint\LessThanZeroException;
use JsonSchema\Types;
use JsonSchema\Walker;
use stdClass;

abstract class AbstractMaxConstraint implements Constraint
{
    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        $keyword = $this->keywords()[0];
        $context->enterNode($schema->{$keyword}, $keyword);

        if (!is_int($schema->{$keyword})) {
            throw new InvalidTypeException($context, Types::TYPE_INTEGER);
        }

        if ($schema->{$keyword} <= 0) {
            throw new LessThanZeroException($context);
        }

        $context->leaveNode();
    }
}
