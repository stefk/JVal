<?php

namespace JsonSchema\Constraint;

use JsonSchema\Context;
use JsonSchema\Walker;
use stdClass;

class AnyOfConstraint extends AbstractOfConstraint
{
    public function keywords()
    {
        return ['anyOf'];
    }

    public function supports($type)
    {
        return true;
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        $accumulatingContext = $context->duplicate();
        $hasMatch = false;

        foreach ($schema->anyOf as $subSchema) {
            $violationCount = count($accumulatingContext->getViolations());
            $walker->applyConstraints($instance, $subSchema, $accumulatingContext);

            if (count($accumulatingContext->getViolations()) === $violationCount) {
                $hasMatch = true;
                break;
            }
        }

        if (!$hasMatch) {
            $context->mergeViolations($accumulatingContext);
            $context->addViolation('instance must match at least one of the schemas listed in anyOf');
        }
    }
}
