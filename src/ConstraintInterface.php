<?php

namespace JsonSchema;

use stdClass;

interface ConstraintInterface
{
    /**
     * Returns the keywords which trigger the constraint check.
     *
     * @return string[]
     */
    function keywords();

    /**
     * Returns whether the constraint is applicable to a given instance.
     * If not, the validation for that constraint should be considered
     * successful event without applying it (4.1).
     *
     * @param mixed $instance
     * @return bool
     */
    function isApplicableTo($instance);

    /**
     * Applies the constraint to the given instance, and populates
     * the execution context with any encountered errors. The current
     * schema is passed in so that dependent keywords can be checked if
     * needed.
     *
     * @param mixed     $instance
     * @param stdClass  $schema
     * @param Context   $context
     * @param Walker    $walker
     */
    function apply($instance, stdClass $schema, Context $context, Walker $walker);
}
