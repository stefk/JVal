<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class ExclusiveMaximumNotBooleanException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'exclusiveMaximum must be a boolean';
    }
}
