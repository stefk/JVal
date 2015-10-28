<?php

namespace JVal\Exception\Resolver;

use JVal\Exception\ResolverException;

class InvalidSegmentTypeException extends ResolverException
{
    protected function buildMessage(array $parameters)
    {
        return vsprintf('Invalid segment type at position %s in pointer "%s"', $parameters);
    }
}
