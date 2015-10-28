<?php

namespace JVal\Constraint;

use JVal\Context;
use JVal\Walker;
use stdClass;

/**
 * Constraint for the "allOf" keyword.
 */
class AllOfConstraint extends AbstractOfConstraint
{
    /**
     * {@inheritDoc}
     */
    public function keywords()
    {
        return ['allOf'];
    }

    /**
     * {@inheritDoc}
     */
    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        $originalCount = $context->countViolations();

        foreach ($schema->allOf as $subSchema) {
            $walker->applyConstraints($instance, $subSchema, $context);
        }

        if ($context->countViolations() > $originalCount) {
            $context->addViolation('instance must match all the schemas listed in allOf');
        }
    }
}
