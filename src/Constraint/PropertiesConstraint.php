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
     * {@inheritdoc}
     */
    public function keywords()
    {
        return ['properties', 'additionalProperties', 'patternProperties'];
    }

    /**
     * {@inheritdoc}
     */
    public function supports($type)
    {
        return $type === Types::TYPE_OBJECT;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        $this->createDefaults($schema);

        $context->enterNode($schema->properties, 'properties');
        $this->parsePropertiesProperty($schema, $context, $walker);

        $context->enterSibling($schema->additionalProperties, 'additionalProperties');
        $this->parseAdditionalPropertiesProperty($schema, $context, $walker);

        $context->enterSibling($schema->patternProperties, 'patternProperties');
        $this->parsePatternPropertiesProperty($schema, $context, $walker);
        $context->leaveNode();
    }

    /**
     * {@inheritdoc}
     */
    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        $this->validateInstance($instance, $schema, $context);
        $this->validateChildren($instance, $schema, $context, $walker);
    }

    private function createDefaults(stdClass $schema)
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
    }

    private function parsePropertiesProperty(stdClass $schema, Context $context, Walker $walker)
    {
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
    }

    private function parseAdditionalPropertiesProperty(stdClass $schema, Context $context, Walker $walker)
    {
        if (is_object($schema->additionalProperties)) {
            $walker->parseSchema($schema->additionalProperties, $context);
        } elseif (!is_bool($schema->additionalProperties)) {
            throw new InvalidTypeException($context, [Types::TYPE_OBJECT, Types::TYPE_BOOLEAN]);
        }
    }

    private function parsePatternPropertiesProperty(stdClass $schema, Context $context, Walker $walker)
    {
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
    }

    private function validateInstance($instance, stdClass $schema, Context $context)
    {
        // implementation of the algorithm described in 5.4.4.4
        $propertySet = array_keys(get_object_vars($schema->properties));
        $patternPropertySet = array_keys(get_object_vars($schema->patternProperties));

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
    }

    private function validateChildren($instance, stdClass $schema, Context $context, Walker $walker)
    {
        // implementation of the algorithm described in 8.3
        $propertySet = array_keys(get_object_vars($schema->properties));
        $patternPropertySet = array_keys(get_object_vars($schema->patternProperties));

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
