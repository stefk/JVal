<?php

namespace JsonSchema\Exception\Constraint;

use JsonSchema\Exception\ConstraintException;

class NotPrimitiveTypeException extends ConstraintException
{
    protected function buildMessage()
    {
        $this->message = sprintf('"%s" must be a primitive type', $this->getTargetNode());
    }
}
