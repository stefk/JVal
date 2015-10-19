<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class TypeElementNotStringException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return vsprintf('type element at index %s is not a string', $parameters);
    }
}
