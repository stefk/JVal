<?php

namespace JVal\Exception\Resolver;

use JVal\Exception\ResolverException;

class InvalidRemoteSchemaException extends ResolverException
{
    protected function buildMessage(array $parameters)
    {
        return vsprintf('Content fetched at "%s" is not a valid schema', $parameters);
    }
}
