<?php

namespace JsonSchema\Exception\Resolver;

use JsonSchema\Exception\ResolverException;

class InvalidRemoteSchemaException extends ResolverException
{
    protected function buildMessage(array $parameters)
    {
        return vsprintf('Content fetched at "%s" is not a valid schema', $parameters);
    }
}
