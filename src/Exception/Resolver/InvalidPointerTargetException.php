<?php

namespace JVal\Exception\Resolver;

use JVal\Exception\ResolverException;

class InvalidPointerTargetException extends ResolverException
{
    protected function buildMessage(array $parameters)
    {
        return vsprintf('Target of pointer "%s" is not a valid schema', $parameters);
    }
}
