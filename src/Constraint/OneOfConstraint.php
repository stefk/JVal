<?php

namespace JsonSchema\Constraint;

use JsonSchema\Context;
use JsonSchema\Walker;
use stdClass;

class OneOfConstraint extends AbstractOfConstraint
{
    public function keywords()
    {
        return ['oneOf'];
    }

    public function supports($type)
    {
        return true;
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        $violationCount = count($context->getViolations());
        $hasMatch = false;
        $hasDoubleMatch = false;

        foreach ($schema->oneOf as $subSchema) {
            $subContext = $context->duplicate();
            $walker->applyConstraints($instance, $subSchema, $subContext);

            if (count($subContext->getViolations()) === $violationCount) {
                if (!$hasMatch) {
                    $hasMatch = true;
                } else {
                    $hasDoubleMatch = true;
                    break;
                }
            }
        }

        if (!$hasMatch || $hasDoubleMatch) {
            $context->addViolation('instance must match exactly one of the schemas listed in oneOf');
        }
    }
}
