<?php

namespace JsonSchema\Exception;

class UnsupportedTypeException extends \Exception
{
    public function __construct($type)
    {
        parent::__construct(sprintf('Unsupported type "%s"', $type));
    }
}
