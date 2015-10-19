<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class PropertyValueNotObjectException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'property value must be an object';
    }
}
