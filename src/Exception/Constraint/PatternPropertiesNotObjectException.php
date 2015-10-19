<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class PatternPropertiesNotObjectException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'patternProperties must be an object';
    }
} 
