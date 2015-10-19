<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class OneOfEmptyException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'oneOf must have at least one element';
    }
}
