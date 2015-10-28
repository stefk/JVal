<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal\Constraint;

use JVal\Constraint;
use JVal\Context;
use JVal\Exception\Constraint\InvalidRegexException;
use JVal\Exception\Constraint\InvalidTypeException;
use JVal\Types;
use JVal\Utils;
use JVal\Walker;
use stdClass;

/**
 * Constraint for the "properties", "additionalProperties" and
 * "patternProperties" keywords.
 */
class PropertiesConstraint implements Constraint
{
    /**
     * {@inheritDoc}
     */
    public function keywords()
    {
        return ['properties', 'additionalProperties', 'patternProperties'];
    }

    /**
     * {@inheritDoc}
     */
    public function supports($type)
    {
        return $type === Types::TYPE_OBJECT;
    }

    /**
     * {@inheritDoc}
     */
    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        if (!property_exists($schema, 'properties')) {
            $schema->properties = new stdClass();
        }

        if (!property_exists($schema, 'additionalProperties')
            || $schema->additionalProperties === true) {
            $schema->additionalProperties = new stdClass();
        }

        if (!property_exists($schema, 'patternProperties')) {
            $schema->patternProperties = new stdClass();
        }

        $context->enterNode($schema->properties, 'properties');

        if (!is_object($schema->properties)) {
            throw new InvalidTypeException($context, Types::TYPE_OBJECT);
        }

        foreach ($schema->properties as $property => $value) {
            $context->enterNode($schema->properties->{$property}, $property);

            if (!is_object($value)) {
                throw new InvalidTypeException($context, Types::TYPE_OBJECT);
            }

            $walker->parseSchema($value, $context);
            $context->leaveNode();
        }

        $context->enterSibling($schema->additionalProperties, 'additionalProperties');

        if (is_object($schema->additionalProperties)) {
            $walker->parseSchema($schema->additionalProperties, $context);
        } elseif (!is_bool($schema->additionalProperties)) {
            throw new InvalidTypeException($context, [Types::TYPE_OBJECT, Types::TYPE_BOOLEAN]);
        }

        $context->enterSibling($schema->patternProperties, 'patternProperties');

        if (!is_object($schema->patternProperties)) {
            throw new InvalidTypeException($context, Types::TYPE_OBJECT);
        }

        foreach ($schema->patternProperties as $regex => $value) {
            $context->enterNode($value, $regex);

            if (!Utils::isValidRegex($regex)) {
                throw new InvalidRegexException($context);
            }

            if (!is_object($value)) {
                throw new InvalidTypeException($context, Types::TYPE_OBJECT);
            }

            $walker->parseSchema($value, $context);
            $context->leaveNode();
        }

        $context->leaveNode();
    }

    /**
     * {@inheritDoc}
     */
    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        $propertySet = array_keys(get_object_vars($schema->properties));
        $patternPropertySet = array_keys(get_object_vars($schema->patternProperties));

        // 1) validation of the instance itself (algorithm described in 5.4.4.4)
        if ($schema->additionalProperties === false) {
            $instanceSet = array_keys(get_object_vars($instance));

            foreach ($propertySet as $property) {
                if (in_array($property, $instanceSet)) {
                    unset($instanceSet[array_search($property, $instanceSet)]);
                }
            }

            foreach ($patternPropertySet as $regex) {
                foreach ($instanceSet as $index => $property) {
                    if (Utils::matchesRegex($property, $regex)) {
                        unset($instanceSet[$index]);
                    }
                }
            }

            if (count($instanceSet) > 0) {
                $context->addViolation('additional properties are not allowed');
            }
        }

        // 2) validation of the instance's children (algorithm described in 8.3)
        foreach ($instance as $property => $value) {
            $context->enterNode($value, $property);
            $schemas = [];

            if (in_array($property, $propertySet)) {
                $schemas[] = $schema->properties->{$property};
            }

            foreach ($patternPropertySet as $regex) {
                if (Utils::matchesRegex($property, $regex)) {
                    $schemas[] = $schema->patternProperties->{$regex};
                }
            }

            if (count($schemas) === 0 && is_object($schema->additionalProperties)) {
                $schemas[] = $schema->additionalProperties;
            }

            foreach ($schemas as $childSchema) {
                $walker->applyConstraints($value, $childSchema, $context);
            }

            $context->leaveNode();
        }
    }
}
