<?php

namespace JsonSchema\Constraint;

use JsonSchema\ConstraintInterface;
use JsonSchema\Context;
use stdClass;

class MaxItemsConstraint implements ConstraintInterface
{
    public function keywords()
    {

    }

    public function isApplicableTo($instance)
    {
        //return instance == array;
    }

     // will be applied to array itself
    public function apply($instance, stdClass $schema, Context $context)
    {
        // if type of instance != array, return (4.1)

        // if array size > maxItems, add error (5.3.2.2)
    }
}
