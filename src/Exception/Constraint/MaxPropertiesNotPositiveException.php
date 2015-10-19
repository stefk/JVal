<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class MaxPropertiesNotPositiveException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'maxProperties must be greater than 0';
    }
}
