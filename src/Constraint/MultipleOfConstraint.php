<?php

namespace JsonSchema\Constraint;

use JsonSchema\ConstraintInterface;
use JsonSchema\Context;
use stdClass;

class MultipleOfConstraint implements ConstraintInterface
{
    public function keywords()
    {

    }

    public function isApplicableTo($instance)
    {
        // return instance == number
    }

    public function apply($instance, stdClass $schema, Context $context)
    {
        // if type of instance != number, return (4.1)

        // if instance / multipleOf != integer, add error to context (5.1.1.2)
    }
}
