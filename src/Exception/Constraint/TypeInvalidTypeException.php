<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class TypeInvalidTypeException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'type must be a string or an array';
    }
}
