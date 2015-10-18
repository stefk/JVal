<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;
use JsonSchema\Types;
use JsonSchema\Walker;
use stdClass;

class ItemsConstraint implements Constraint
{
    public function keywords()
    {
        return ['items', 'additionalItems'];
    }

    public function supports($type)
    {
        return $type === Types::TYPE_ARRAY;
    }

    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        if (!isset($schema->items)) {
            $schema->items = new stdClass();
        }

        if (!isset($schema->additionalItems) || $schema->additionalItems === true) {
            $schema->additionalItems = new stdClass();
        }

        $startPath = $context->getCurrentPath();

        if (is_object($schema->items)) {
            $context->setNode($schema->items, $startPath . '/items');
            $walker->parseSchema($schema->items, $context);
        } elseif (is_array($schema->items)) {
            foreach ($schema->items as $index => $item) {
                if (!is_object($item)) {
                    throw new ConstraintException(
                        'items element must be an object',
                        ConstraintException::ITEMS_ELEMENT_NOT_OBJECT,
                        $context
                    );
                }

                $context->setNode($schema->items, $startPath . '/items/' . ($index + 1));
                $walker->parseSchema($item, $context);
            }
        } else {
            throw new ConstraintException(
                'items must be an object or an array',
                ConstraintException::ITEMS_INVALID_TYPE,
                $context
            );
        }

        if (is_object($schema->additionalItems)) {
            $context->setNode($schema->items, $startPath . '/additionalItems');
            $walker->parseSchema($schema->additionalItems, $context);
        } elseif (!is_bool($schema->additionalItems)) {
            throw new ConstraintException(
                'additionalItems must be an object or a boolean',
                ConstraintException::ADDITIONAL_ITEMS_INVALID_TYPE,
                $context
            );
        }

        $context->setNode($schema, $startPath);
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        $startPath = $context->getCurrentPath();

        if (is_object($schema->items)) {
            // 8.2.3.1. If items is a schema, then the child instance must be
            // valid against this schema, regardless of its index, and
            // regardless of the value of "additionalItems".
            foreach ($instance as $index => $item) {
                $context->setNode($item, $startPath . '/' . ($index + 1));
                $walker->applyConstraints($item, $schema->items, $context);
            }
        } else { // "items" is an array
            $itemSize = count($schema->items);

            foreach ($instance as $index => $item) {
                $context->setNode($item, $startPath . '/' . ($index + 1));

                // 8.2.3.2.  If the index is less than, or equal to, the size of
                // "items", the child instance must be valid against the
                // corresponding schema in the "items" array; otherwise, it must
                // be valid against the schema defined by "additionalItems".
                //
                // NOTE: this is adapted for 0-based indexation.
                if ($index < $itemSize) {
                    $walker->applyConstraints($item, $schema->items[$index], $context);
                } elseif ($schema->additionalItems === false) {
                    $context->addViolation('additional items are not allowed');
                } else {
                    $walker->applyConstraints($item, $schema->additionalItems, $context);
                }
            }
        }

        $context->setNode($schema, $startPath);
    }
}
