<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class PropertiesNotObjectException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'properties must be an object';
    }
}
