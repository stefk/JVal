<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Exception\ConstraintException;

class NotStrictlyPositiveException extends ConstraintException
{
    protected function buildMessage()
    {
        $this->message = sprintf('%s must be greater than 0', $this->getTargetNode());
    }
}
