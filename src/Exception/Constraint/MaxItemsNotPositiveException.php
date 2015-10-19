<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class MaxItemsNotPositiveException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'maxItems must be greater than 0';
    }
}
