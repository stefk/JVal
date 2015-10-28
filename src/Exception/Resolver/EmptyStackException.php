<?php

namespace JVal\Exception\Resolver;

use JVal\Exception\ResolverException;

class EmptyStackException extends ResolverException
{
    protected function buildMessage(array $parameters)
    {
        return 'Resolution context stack is empty';
    }
}
