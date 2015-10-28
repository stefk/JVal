<?php

namespace JVal\Exception\Constraint;

use JVal\Context;
use JVal\Exception\ConstraintException;

class InvalidTypeException extends ConstraintException
{
    private $expectedType;

    public function __construct(Context $context, $expected)
    {
        $this->expectedType = $expected;
        parent::__construct($context);
    }

    protected function buildMessage()
    {
        if (is_array($this->expectedType)) {
            $this->message = sprintf(
                '%s type must be one of the following: %s',
                $this->getTargetNode(),
                implode(', ', $this->expectedType)
            );
        } else {
            $this->message = sprintf(
                '%s must be of type %s',
                $this->getTargetNode(),
                $this->expectedType
            );
        }
    }
}
