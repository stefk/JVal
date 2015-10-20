<?php

namespace JsonSchema\Exception;

use JsonSchema\Context;

abstract class ConstraintException extends \Exception
{
    private $context;
    private $target;

    public function __construct(Context $context, $target = null)
    {
        parent::__construct();
        $this->context = $context;
        $this->target = $target;
        $this->buildMessage();
    }

    public function getPath()
    {
        return $this->context->getCurrentPath();
    }

    public function getTarget()
    {
        return $this->target;
    }

    abstract protected function buildMessage();

    /**
     * Returns a printable representation of the exception
     * target. If no target has been explicitly passed in,
     * the last non-array segments of the path are returned.
     *
     * @return string
     */
    protected function getTargetNode()
    {
        if ($this->target === null) {
            $segments = explode('/', $this->getPath());
            $target = '';

            while (count($segments) > 0) {
                $segment = array_pop($segments);
                $target = $segment . '/' . $target;

                if (!is_numeric($segment)) {
                    break;
                }
            }

            return rtrim($target, '/');
        }

        return $this->target;
    }
}
