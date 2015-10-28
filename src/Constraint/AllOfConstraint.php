<?php

namespace JVal\Constraint;

use JVal\Context;
use JVal\Walker;
use stdClass;

class AllOfConstraint extends AbstractOfConstraint
{
    public function keywords()
    {
        return ['allOf'];
    }

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
