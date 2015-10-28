<?php

namespace JVal\Exception\Resolver;

use JVal\Exception\ResolverException;

class SelfReferencingPointerException extends ResolverException
{
    protected function buildMessage(array $parameters)
    {
        return 'Pointer self reference detected';
    }
}
