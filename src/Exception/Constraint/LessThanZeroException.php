<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Exception\ConstraintException;

class LessThanZeroException extends ConstraintException
{
    protected function buildMessage()
    {
        $this->message = sprintf(
            '%s must be greater than or equal to 0',
            $this->getTargetNode()
        );
    }
}
