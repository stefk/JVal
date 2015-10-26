<?php

namespace JsonSchema;

use stdClass;

interface Constraint
{
    /**
     * Returns the keywords which trigger the constraint check.
     *
     * @return string[]
     */
    function keywords();

    /**
     * Returns whether the constraint is applicable to a given type.
     * If not, the validation of that constraint should be considered
     * successful event without applying it (4.1). Type passed in is
     * one of the primitive types listed in Types::TYPE_*.
     *
     * @param string $type
     * @return bool
     */
    function supports($type);

    /**
     * Ensures the supported keywords have a valid value in a given
     * schema, and sets default ones if necessary. This method should
     * be called only if one of the triggering keywords has been 
     * encountered in the schema.
     *
     * @param stdClass  $schema
     * @param Context   $context
     * @param Walker    $walker
     */
    function normalize(stdClass $schema, Context $context, Walker $walker);

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
