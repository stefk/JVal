<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class AllOfEmptyException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'allOf must have at least one element';
    }
}
