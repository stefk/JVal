<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class AnyOfNotArrayException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'anyOf must be an array';
    }
}
