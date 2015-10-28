<?php

namespace JVal\Exception\Constraint;

use JVal\Exception\ConstraintException;

class NotPrimitiveTypeException extends ConstraintException
{
    protected function buildMessage()
    {
        $this->message = sprintf('"%s" must be a primitive type', $this->getTargetNode());
    }
}
