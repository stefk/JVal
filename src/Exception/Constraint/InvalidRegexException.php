<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Exception\ConstraintException;

class InvalidRegexException extends ConstraintException
{
    protected function buildMessage()
    {
        $this->message = 'regex is invalid or not supported';
    }
}
