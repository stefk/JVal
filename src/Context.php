<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal;

/**
 * Stores data related to a particular validation task (default schema version,
 * accumulated violations, current path, etc.).
 */
class Context
{
    /**
     * @var string
     */
    private $version = Registry::VERSION_DRAFT_4;

    /**
     * @var array
     */
    private $violations = [];

    /**
     * @var array
     */
    private $pathSegments = [];

    /**
     * Pushes a path segment onto the context stack, making it the current
     * visited node.
     *
     * @param string $pathSegment
     */
    public function enterNode($pathSegment)
    {
        $this->pathSegments[] = $pathSegment;
    }


    /**
     * Leaves the current node and enters another node located at the same
     * depth in the hierarchy.
     *
     * @param string $pathSegment
     */
    public function enterSibling($pathSegment)
    {
        $this->leaveNode();
        $this->enterNode($pathSegment);
    }

    /**
     * Removes the current node from the context stack, thus returning to the
     * previous (parent) node.
     */
    public function leaveNode()
    {
        if (count($this->pathSegments) === 0) {
            throw new \LogicException('Cannot leave node: instance stack is empty');
        }

        array_pop($this->pathSegments);
    }

    /**
     * Returns the path of the current node.
     *
     * @return string
     */
    public function getCurrentPath()
    {
        return $this->pathSegments ? '/'.implode('/', $this->pathSegments) : '';
    }

    /**
     * Adds a violation message for the current node.
     *
     * @param string $message
     * @param array  $parameters
     */
    public function addViolation($message, array $parameters = [])
    {
        $this->violations[] = [
            'path' => $this->getCurrentPath(),
            'message' => vsprintf($message, $parameters),
        ];
    }

    /**
     * Returns the list of accumulated violations.
     *
     * @return array
     */
    public function getViolations()
    {
        return $this->violations;
    }

    /**
     * Returns the number of accumulated violations.
     *
     * @return int
     */
    public function countViolations()
    {
        return count($this->violations);
    }

    /**
     * Returns the current schema version.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Sets the current schema version.
     *
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Returns a copy of the context, optionally purged of its
     * accumulated violations.
     *
     * @param bool $withViolations
     * @return Context
     */
    public function duplicate($withViolations = true)
    {
        // cloning as long as the context doesn't hold object references
        $clone = clone $this;

        if (!$withViolations) {
            $clone->purgeViolations();
        }

        return clone $this;
    }

    /**
     * Merges the current violations with the violations stored in
     * another context.
     *
     * @param Context $context
     */
    public function mergeViolations(Context $context)
    {
        $this->violations = array_merge($this->violations, $context->getViolations());
    }

    /**
     * Deletes the list of accumulated violations.
     */
    public function purgeViolations()
    {
        $this->violations = [];
    }
}
