<?php

namespace JVal\Constraint;

use JVal\Constraint;
use JVal\Context;
use JVal\Exception\Constraint\EmptyArrayException;
use JVal\Exception\Constraint\InvalidTypeException;
use JVal\Types;
use JVal\Walker;
use stdClass;

abstract class AbstractOfConstraint implements Constraint
{
    public function supports($type)
    {
        return true;
    }

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
            $context->enterNode($subSchema, $index);

            if (!is_object($subSchema)) {
                throw new InvalidTypeException($context, Types::TYPE_OBJECT);
            }

            $walker->parseSchema($subSchema, $context);
            $context->leaveNode();
        }

        $context->leaveNode();
    }
}
