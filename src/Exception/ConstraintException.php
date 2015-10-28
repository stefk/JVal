<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal\Exception;

use JVal\Context;

/**
 * Base class for constraint exceptions.
 */
abstract class ConstraintException extends \Exception
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var mixed
     */
    private $target;

    /**
     * Constructor.
     *
     * @param Context   $context    The current validation context
     * @param mixed     $target     An optional exception target not
     *                              present in the current context
     */
    public function __construct(Context $context, $target = null)
    {
        parent::__construct();
        $this->context = $context;
        $this->target = $target;
        $this->buildMessage();
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->context->getCurrentPath();
    }

    /**
     * @return mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Builds the exception message.
     */
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
        if (null === $target = $this->getTarget()) {
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

        return $target;
    }
}
