<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal\Exception;

/**
 * Base class for URI resolution exception.
 */
abstract class ResolverException extends \Exception
{
    /**
     * Constructor.
     *
     * @param array $parameters The exception message parameters
     */
    public function __construct(array $parameters = [])
    {
        parent::__construct($this->buildMessage($parameters));
    }

    /**
     * Builds the exception message.
     *
     * @param array $parameters
     */
    abstract protected function buildMessage(array $parameters);
}
