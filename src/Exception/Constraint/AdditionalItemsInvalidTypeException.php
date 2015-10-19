<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class AdditionalItemsInvalidTypeException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'additionalItems must be an object or a boolean';
    }
}
