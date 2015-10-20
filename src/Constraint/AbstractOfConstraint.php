<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\Constraint\EmptyArrayException;
use JsonSchema\Exception\Constraint\InvalidTypeException;
use JsonSchema\Types;
use JsonSchema\Walker;
use stdClass;

abstract class AbstractOfConstraint implements Constraint
{
    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        $keyword = $this->keywords()[0];
        $context->enterNode($schema->{$keyword}, $keyword);

        if (!is_array($schema->{$keyword})) {
            throw new InvalidTypeException($context, Types::TYPE_ARRAY);
        }

        if (count($schema->{$keyword}) === 0) {
            throw new EmptyArrayException($context);
        }

        foreach ($schema->{$keyword} as $index => $subSchema) {
            $context->enterNode($subSchema, $index + 1);

            if (!is_object($subSchema)) {
                throw new InvalidTypeException($context, Types::TYPE_OBJECT);
            }

            $walker->parseSchema($subSchema, $context);
            $context->leaveNode();
        }

        $context->leaveNode();
    }
}
