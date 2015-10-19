<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;

class TypeElementNotUniqueException extends ConstraintException
{
    protected function buildMessage(Context $context, array $parameters)
    {
        return 'type elements must be unique';
    }
}
