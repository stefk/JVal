<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal\Exception\Resolver;

use JVal\Exception\ResolverException;

class InvalidSegmentTypeException extends ResolverException
{
    protected function buildMessage(array $parameters)
    {
        return vsprintf('Invalid segment type at position %s in pointer "%s"', $parameters);
    }
}
