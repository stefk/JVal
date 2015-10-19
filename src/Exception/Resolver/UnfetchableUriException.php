<?php

namespace JsonSchema\Exception\Resolver;

use JsonSchema\Exception\ResolverException;

class UnfetchableUriException extends ResolverException
{
    protected function buildMessage(array $parameters)
    {
        return vsprintf('Failed to fetch URI "%s" (error: "%s", severity: %s)', $parameters);
    }
}
