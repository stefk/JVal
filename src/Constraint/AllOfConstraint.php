<?php

namespace JsonSchema\Constraint;

use JsonSchema\Context;
use JsonSchema\Walker;
use stdClass;

class AllOfConstraint extends AbstractOfConstraint
{
    public function keywords()
    {
        return ['allOf'];
    }

    public function supports($type)
    {
        return true;
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
