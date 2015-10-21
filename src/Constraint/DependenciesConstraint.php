<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\Constraint\EmptyArrayException;
use JsonSchema\Exception\Constraint\InvalidTypeException;
use JsonSchema\Exception\Constraint\NotUniqueException;
use JsonSchema\Types;
use JsonSchema\Walker;
use stdClass;

class DependenciesConstraint implements Constraint
{
    public function keywords()
    {
        return ['dependencies'];
    }

    public function supports($type)
    {
        return $type === Types::TYPE_OBJECT;
    }

    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        $context->enterNode($schema->dependencies, 'dependencies');

        if (!is_object($schema->dependencies)) {
            throw new InvalidTypeException($context, Types::TYPE_OBJECT);
        }

        foreach ($schema->dependencies as $property => $value) {
            $context->enterNode($value, $property);

            if (is_object($value)) {
                $walker->parseSchema($value, $context);
            } elseif (is_array($value)) {
                if (0 === $propertyCount = count($value)) {
                    throw new EmptyArrayException($context);
                }

                foreach ($value as $index => $subProperty) {
                    if (!is_string($subProperty)) {
                        $context->enterNode($subProperty, $index + 1);

                        throw new InvalidTypeException($context, Types::TYPE_STRING);
                    }
                }

                if (count(array_unique($value)) !== $propertyCount) {
                    throw new NotUniqueException($context);
                }
            } else {
                throw new InvalidTypeException($context, [Types::TYPE_OBJECT, Types::TYPE_ARRAY]);
            }

            $context->leaveNode();
        }

        $context->leaveNode();
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        foreach ($schema->dependencies as $property => $value) {
            if (isset($instance->{$property})) {
                if (is_object($value)) {
                    // 5.4.5.2.1. Schema dependencies
                    $walker->applyConstraints($instance, $value, $context);
                } else {
                    // 5.4.5.2.2. Property dependencies
                    foreach ($value as $propertyDependency) {
                        if (!isset($instance->{$propertyDependency})) {
                            $context->addViolation(
                                'dependency property "%s" is missing',
                                [$propertyDependency]
                            );
                        }
                    }
                }
            }
        }
    }
}
