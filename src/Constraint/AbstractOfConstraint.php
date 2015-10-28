<?php

namespace JVal\Constraint;

use JVal\Constraint;
use JVal\Context;
use JVal\Exception\Constraint\EmptyArrayException;
use JVal\Exception\Constraint\InvalidTypeException;
use JVal\Types;
use JVal\Walker;
use stdClass;

/**
 * Base class for constraints based on a set of sub-schemas.
 */
abstract class AbstractOfConstraint implements Constraint
{
    /**
     * {@inheritDoc}
     */
    public function supports($type)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
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
