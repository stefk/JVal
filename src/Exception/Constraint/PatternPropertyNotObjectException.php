<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class PatternPropertyNotObjectException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'patternProperties property value must be an object';
    }
} 
