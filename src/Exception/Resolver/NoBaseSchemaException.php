<?php

namespace JsonSchema\Exception\Resolver;

use JsonSchema\Exception\ResolverException;

class NoBaseSchemaException extends ResolverException
{
    protected function buildMessage(array $parameters)
    {
        return 'No base schema has been set';
    }
}
