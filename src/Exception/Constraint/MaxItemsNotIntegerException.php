<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class MaxItemsNotIntegerException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'maxItems must be an integer';
    }
}
