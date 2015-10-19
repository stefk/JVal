<?php

namespace JsonSchema\Exception\Resolver;

use JsonSchema\Exception\ResolverException;

class InvalidSegmentTypeException extends ResolverException
{
    protected function buildMessage(array $parameters)
    {
        return vsprintf('Invalid segment type at position %s in pointer "%s"', $parameters);
    }
}
