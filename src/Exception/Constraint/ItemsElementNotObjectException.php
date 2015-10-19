<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class ItemsElementNotObjectException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'items element must be an object';
    }
}
