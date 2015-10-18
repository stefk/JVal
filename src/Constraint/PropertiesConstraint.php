<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Types;
use JsonSchema\Walker;
use stdClass;

class PropertiesConstraint implements Constraint
{
    public function keywords()
    {
        return ['properties', 'additionalProperties', 'patternProperties'];
    }

    public function supports($type)
    {
        return $type === Types::TYPE_OBJECT;
    }

    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        /*
If either "properties" or "patternProperties" are absent, they can be considered present with an empty object as a value.

If "additionalProperties" is absent, it may be considered present with an empty schema as a value.
         */

        /*
If "properties" or "patternProperties" are absent, they are considered present with an empty object as a value.

If "additionalProperties" is absent, it is considered present with an empty schema as a value. In addition, boolean value true is considered equivalent to an empty schema.
         */


        /*
The value of "additionalProperties" MUST be a boolean or an object. If it is an object, it MUST also be a valid JSON Schema.

The value of "properties" MUST be an object. Each value of this object MUST be an object, and each object MUST be a valid JSON Schema.

The value of "patternProperties" MUST be an object. Each property name of this object SHOULD be a valid regular expression, according to the ECMA 262 regular expression dialect. Each property value of this object MUST be an object, and each object MUST be a valid JSON Schema.
         */
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {

    }
}
