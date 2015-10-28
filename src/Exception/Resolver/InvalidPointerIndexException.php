<?php

namespace JVal\Exception\Resolver;

use JVal\Exception\ResolverException;

class InvalidPointerIndexException extends ResolverException
{
    protected function buildMessage(array $parameters)
    {
        return vsprintf(
            'Invalid index "%s" at position %s in pointer "%s"',
            $parameters
        );
    }
}
