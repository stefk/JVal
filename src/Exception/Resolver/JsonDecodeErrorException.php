<?php

namespace JsonSchema\Exception\Resolver;

use JsonSchema\Exception\ResolverException;

class JsonDecodeErrorException extends ResolverException
{
    protected function buildMessage(array $parameters)
    {
        return vsprintf('Cannot decode JSON from URI "%s" (error: %s)', $parameters);
    }
}
