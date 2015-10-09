<?php

namespace JsonSchema\Constraint;

use JsonSchema\ConstraintInterface;
use JsonSchema\Context;
use stdClass;

class ItemConstraint implements ConstraintInterface
{
    public function keywords()
    {

    }

    // will be applied to array *items* (-> any type)
    public function isApplicableTo($instance)
    {
        // return true
    }

    public function apply($instance, stdClass $schema, Context $context)
    {
        // if items is not set, return (5.3.1.2)

        // if additionalItems is true or object, return [VAGUE "object", what if complete schema ? or no instances corresponding to defined items ?]

        // if additionalItems == false and items == array and array size > items size, add error
    }
}
