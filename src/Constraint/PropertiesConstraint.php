<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;
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
            throw new ConstraintException(
                'properties must be an object',
                ConstraintException::PROPERTIES_NOT_OBJECT,
                $context
            );
        }

        foreach ($schema->properties as $property => $value) {
            $context->setNode($schema->properties, "{$startPath}/properties/{$property}");

            if (!is_object($value)) {
                throw new ConstraintException(
                    'property value must be an object',
                    ConstraintException::PROPERTY_VALUE_NOT_OBJECT,
                    $context
                );
            }

            $walker->parseSchema($value, $context);
        }

        $context->setNode($schema->additionalProperties, "{$startPath}/additionalProperties");

        if (is_object($schema->additionalProperties)) {
            $walker->parseSchema($schema->additionalProperties, $context);
        } elseif (!is_bool($schema->additionalProperties)) {
            throw new ConstraintException(
                'additionalProperties must be an object or a boolean',
                ConstraintException::ADDITIONAL_PROPERTIES_INVALID_TYPE,
                $context
            );
        }

        $context->setNode($schema->patternProperties, "{$startPath}/patternProperties");

        if (!is_object($schema->patternProperties)) {
            throw new ConstraintException(
                'patternProperties must be an object',
                ConstraintException::PATTERN_PROPERTIES_NOT_OBJECT,
                $context
            );
        }

        foreach ($schema->patternProperties as $regex => $value) {
            $context->setNode($schema->patternProperties, "{$startPath}/patternProperties/{$regex}");

            if (@preg_match("/{$regex}/", '') === false) {
                throw new ConstraintException(
                    'patternProperties regex is invalid or non supported',
                    ConstraintException::PATTERN_PROPERTIES_INVALID_REGEX,
                    $context
                );
            }

            if (!is_object($value)) {
                throw new ConstraintException(
                    'patternProperties property value must be an object',
                    ConstraintException::PATTERN_PROPERTY_NOT_OBJECT,
                    $context
                );
            }

            $walker->parseSchema($value, $context);
        }

        $context->setNode($schema, $startPath);
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {

    }
}
