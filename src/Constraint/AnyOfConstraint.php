<?php

namespace JVal\Constraint;

use JVal\Context;
use JVal\Walker;
use stdClass;

/**
 * Constraint for the "anyOf" keyword.
 */
class AnyOfConstraint extends AbstractOfConstraint
{
    /**
     * {@inheritDoc}
     */
    public function keywords()
    {
        return ['anyOf'];
    }

    /**
     * {@inheritDoc}
     */
    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        $accumulatingContext = $context->duplicate();
        $hasMatch = false;

        foreach ($schema->anyOf as $subSchema) {
            $originalCount = $accumulatingContext->countViolations();
            $walker->applyConstraints($instance, $subSchema, $accumulatingContext);

            if ($accumulatingContext->countViolations() === $originalCount) {
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
