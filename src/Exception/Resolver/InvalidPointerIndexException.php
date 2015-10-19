<?php

namespace JsonSchema\Exception\Resolver;

use JsonSchema\Exception\ResolverException;

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
