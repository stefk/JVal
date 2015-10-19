<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class AnyOfEmptyException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'anyOf must have at least one element';
    }
}
