<?php

namespace JVal\Exception\Constraint;

use JVal\Exception\ConstraintException;

class NotUniqueException extends ConstraintException
{
    protected function buildMessage()
    {
        $this->message = sprintf('%s elements must be unique', $this->getTargetNode());
    }
}
