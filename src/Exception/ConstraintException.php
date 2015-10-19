<?php

namespace JsonSchema\Exception;

use JsonSchema\Context;

abstract class ConstraintException extends \Exception
{
    private $context;

    public function __construct(Context $context, array $parameters = [])
    {
        $this->context = $context;
        $message = $this->buildMessage($context, $parameters);
        $path = $context->getCurrentPath() === '' ? '/' : $context->getCurrentPath();
        $message = sprintf('%s (path: %s)', $message, $path);
        parent::__construct($message);
    }

    public function getContext()
    {
        return $this->context;
    }

    abstract protected function buildMessage(Context $context, array $parameters);
}
