<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Exception\ConstraintException;

class EmptyArrayException extends ConstraintException
{
    protected function buildMessage()
    {
        $this->message = sprintf(
            '%s must contain at least one element',
            $this->getTargetNode()
        );
    }
}
