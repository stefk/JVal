<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class AllOfNotArrayException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'allOf must be an array';
    }
}
