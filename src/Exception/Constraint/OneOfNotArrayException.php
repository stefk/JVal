<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class OneOfNotArrayException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'oneOf must be an array';
    }
}
