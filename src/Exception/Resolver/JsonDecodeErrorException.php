<?php

namespace JVal\Exception\Resolver;

use JVal\Exception\ResolverException;

class JsonDecodeErrorException extends ResolverException
{
    protected function buildMessage(array $parameters)
    {
        return vsprintf('Cannot decode JSON from URI "%s" (error: %s)', $parameters);
    }
}
