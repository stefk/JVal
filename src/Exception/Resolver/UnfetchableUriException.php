<?php

namespace JVal\Exception\Resolver;

use JVal\Exception\ResolverException;

class UnfetchableUriException extends ResolverException
{
    protected function buildMessage(array $parameters)
    {
        return vsprintf('Failed to fetch URI "%s" (error: "%s", severity: %s)', $parameters);
    }
}
