<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\Constraint\AdditionalPropertiesInvalidTypeException;
use JsonSchema\Exception\Constraint\PatternPropertiesInvalidRegexException;
use JsonSchema\Exception\Constraint\PatternPropertiesNotObjectException;
use JsonSchema\Exception\Constraint\PatternPropertyNotObjectException;
use JsonSchema\Exception\Constraint\PropertiesNotObjectException;
use JsonSchema\Exception\Constraint\PropertyValueNotObjectException;
use JsonSchema\Types;
use JsonSchema\Walker;
use stdClass;

class PropertiesConstraint implements Constraint
{
    public function keywords()
    {
        return ['properties', 'additionalProperties', 'patternProperties'];
    }

    public function supports($type)
    {
        return $type === Types::TYPE_OBJECT;
    }

    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        if (!isset($schema->properties)) {
            $schema->properties = new stdClass();
        }

        if (!isset($schema->additionalProperties) || $schema->additionalProperties === true) {
            $schema->additionalProperties = new stdClass();
        }

        if (!isset($schema->patternProperties)) {
            $schema->patternProperties = new stdClass();
        }

        $startPath = $context->getCurrentPath();
        $context->setNode($schema->properties, "{$startPath}/properties");

        if (!is_object($schema->properties)) {
            throw new PropertiesNotObjectException($context);
        }

        foreach ($schema->properties as $property => $value) {
            $context->setNode($schema->properties, "{$startPath}/properties/{$property}");

            if (!is_object($value)) {
                throw new PropertyValueNotObjectException($context);
            }

            $walker->parseSchema($value, $context);
        }

        $context->setNode($schema->additionalProperties, "{$startPath}/additionalProperties");

        if (is_object($schema->additionalProperties)) {
            $walker->parseSchema($schema->additionalProperties, $context);
        } elseif (!is_bool($schema->additionalProperties)) {
            throw new AdditionalPropertiesInvalidTypeException($context);
        }

        $context->setNode($schema->patternProperties, "{$startPath}/patternProperties");

        if (!is_object($schema->patternProperties)) {
            throw new PatternPropertiesNotObjectException($context);
        }

        foreach ($schema->patternProperties as $regex => $value) {
            $context->setNode($schema->patternProperties, "{$startPath}/patternProperties/{$regex}");

            if (@preg_match("/{$regex}/", '') === false) {
                throw new PatternPropertiesInvalidRegexException($context);
            }

            if (!is_object($value)) {
                throw new PatternPropertyNotObjectException($context);
            }

            $walker->parseSchema($value, $context);
        }

        $context->setNode($schema, $startPath);
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        $startPath = $context->getCurrentPath();
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
                    if (preg_match("/{$regex}/", $property)) {
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
            $context->setNode($value, "{$startPath}/{$property}");
            $schemas = [];

            if (in_array($property, $propertySet)) {
                $schemas[] = $schema->properties->{$property};
            }

            foreach ($patternPropertySet as $regex) {
                if (preg_match("/{$regex}/", $property)) {
                    $schemas[] = $schema->patternProperties->{$regex};
                }
            }

            if (count($schemas) === 0 && is_object($schema->additionalProperties)) {
                $schemas[] = $schema->additionalProperties;
            }

            foreach ($schemas as $childSchema) {
                $walker->applyConstraints($value, $childSchema, $context);
            }
        }

        $context->setNode($instance, $startPath);
    }
}
