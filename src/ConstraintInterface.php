<?php

namespace JsonSchema;

use stdClass;

interface ConstraintInterface
{
    // return the keyword(s) which trigger the constraint
    function keywords();

//    // container ?
//    function scope();

    // validation will be bypassed if not -- always succeed (4.1)
    function isApplicableTo($instance);

    // stateless
    // populates context if error
    // may need schema for dependent keywords
    function apply($instance, stdClass $schema, Context $context);
}
