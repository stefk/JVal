<?php

namespace JVal\Exception\Constraint;

use JVal\Exception\ConstraintException;

class InvalidRegexException extends ConstraintException
{
    protected function buildMessage()
    {
        $this->message = 'regex is invalid or not supported';
    }
}
