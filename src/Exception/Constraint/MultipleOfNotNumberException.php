<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class MultipleOfNotNumberException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'multipleOf must be a number';
    }
} 
