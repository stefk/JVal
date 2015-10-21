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

class RequiredConstraint implements Constraint
{
    public function keywords()
    {
        return ['required'];
    }

    public function supports($type)
    {
        return $type === Types::TYPE_OBJECT;
    }

    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        $context->enterNode($schema->required, 'required');

        if (!is_array($schema->required)) {
            throw new InvalidTypeException($context, Types::TYPE_ARRAY);
        }

        if (0 === $requiredCount = count($schema->required)) {
            throw new EmptyArrayException($context);
        }

        foreach ($schema->required as $index => $property) {
            if (!is_string($property)) {
                $context->enterNode($property, $index + 1);

                throw new InvalidTypeException($context, Types::TYPE_STRING);
            }
        }

        if ($requiredCount !== count(array_unique($schema->required))) {
            throw new NotUniqueException($context);
        }

        $context->leaveNode();
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        foreach ($schema->required as $property) {
            if (!isset($instance->{$property})) {
                $context->addViolation('property "%s" is missing', [$property]);
            }
        }
    }
}
