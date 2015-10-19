<?php

namespace JsonSchema\Exception\Resolver;

use JsonSchema\Exception\ResolverException;

class InvalidPointerTargetException extends ResolverException
{
    protected function buildMessage(array $parameters)
    {
        return vsprintf('Target of pointer "%s" is not a valid schema', $parameters);
    }
}
