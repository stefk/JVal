<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class TypeNotPrimitiveTypeException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return vsprintf('"%s" is not a primitive type', $parameters);
    }
}
