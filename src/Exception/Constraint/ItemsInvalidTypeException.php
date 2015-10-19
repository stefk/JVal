<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class ItemsInvalidTypeException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'items must be an object or an array';
    }
}

