<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class PatternPropertiesInvalidRegexException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'patternProperties regex is invalid or non supported';
    }
} 
