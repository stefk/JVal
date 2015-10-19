<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class OneOfElementNotObjectException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return vsprintf('oneOf element at position %s is not an object', $parameters);
    }
}
