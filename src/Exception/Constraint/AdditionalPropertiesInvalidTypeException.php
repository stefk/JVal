<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class AdditionalPropertiesInvalidTypeException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'additionalProperties must be an object or a boolean';
    }
} 
