<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class AnyOfElementNotObjectException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return vsprintf('anyOf element at position %s is not an object', $parameters);
    }
}
