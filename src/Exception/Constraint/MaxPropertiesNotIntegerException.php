<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class MaxPropertiesNotIntegerException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'maxProperties must be an integer';
    }
} 
