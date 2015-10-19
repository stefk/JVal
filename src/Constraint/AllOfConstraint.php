<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\Constraint\AllOfElementNotObjectException;
use JsonSchema\Exception\Constraint\AllOfEmptyException;
use JsonSchema\Exception\Constraint\AllOfNotArrayException;
use JsonSchema\Walker;
use stdClass;

class AllOfConstraint implements Constraint
{
    public function keywords()
    {
        return ['allOf'];
    }

    public function supports($type)
    {
        return true;
    }

    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        $startPath = $context->getCurrentPath();

        if (!is_array($schema->allOf)) {
            throw new AllOfNotArrayException($context);
        }

        if (count($schema->allOf) === 0) {
            throw new AllOfEmptyException($context);
        }

        foreach ($schema->allOf as $index => $subSchema) {
            $context->setNode($subSchema, "{$startPath}/{$index}");

            if (!is_object($subSchema)) {
                throw new AllOfElementNotObjectException($context, [$index]);
            }

            $walker->parseSchema($subSchema, $context);
        }

        $context->setNode($schema, $startPath);
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        $violationCount = count($context->getViolations());

        foreach ($schema->allOf as $subSchema) {
            $walker->applyConstraints($instance, $subSchema, $context);
        }

        if (count($context->getViolations()) > $violationCount) {
            $context->addViolation('instance must match all the schemas listed in allOf');
        }
    }
}
