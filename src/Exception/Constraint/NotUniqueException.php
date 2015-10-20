<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Exception\ConstraintException;

class NotUniqueException extends ConstraintException
{
    protected function buildMessage()
    {
        $this->message = sprintf('%s elements must be unique', $this->getTargetNode());
    }
}
