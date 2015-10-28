<?php

namespace JVal\Exception\Resolver;

use JVal\Exception\ResolverException;

class UnresolvedPointerPropertyException extends ResolverException
{
    protected function buildMessage(array $parameters)
    {
        return vsprintf(
            'Cannot resolve property "%s" at position %s in pointer "%s"',
            $parameters
        );
    }
}
