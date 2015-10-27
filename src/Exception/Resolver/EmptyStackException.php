<?php

namespace JsonSchema\Exception\Resolver;

use JsonSchema\Exception\ResolverException;

class EmptyStackException extends ResolverException
{
    protected function buildMessage(array $parameters)
    {
        return 'Resolution context stack is empty';
    }
}
