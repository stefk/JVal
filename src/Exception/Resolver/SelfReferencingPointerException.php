<?php

namespace JsonSchema\Exception\Resolver;

use JsonSchema\Exception\ResolverException;

class SelfReferencingPointerException extends ResolverException
{
    protected function buildMessage(array $parameters)
    {
        return 'Pointer self reference detected';
    }
}
