<?php

namespace JsonSchema\Exception\Resolver;

use JsonSchema\Exception\ResolverException;

class UnresolvedPointerIndexException extends ResolverException
{
    protected function buildMessage(array $parameters)
    {
        return vsprintf(
            'Cannot resolve index "%s" at position %s in pointer "%s"',
            $parameters
        );
    }
}
