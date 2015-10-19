<?php

namespace JsonSchema\Exception;

abstract class ResolverException extends \Exception
{
    public function __construct(array $parameters = [])
    {
        parent::__construct($this->buildMessage($parameters));
    }

    abstract protected function buildMessage(array $parameters);
}
