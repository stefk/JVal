<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class MaximumNotPresentException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'maximum must be present';
    }
}
