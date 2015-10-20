<?php

namespace JsonSchema;

class Context
{
    private $version = Registry::VERSION_DRAFT_4;
    private $violations = [];
    private $pathSegments = [];
    private $instanceStack = [];
    private $path = '';

    public function enterNode($instance, $pathSegment)
    {
        $this->instanceStack[] = $instance;
        $this->pathSegments[] = $pathSegment;
        $this->path .= '/' . $pathSegment;
    }

    public function enterSibling($instance, $pathSegment)
    {
        $this->leaveNode();
        $this->enterNode($instance, $pathSegment);
    }

    public function leaveNode()
    {
        if (count($this->instanceStack) === 0) {
            throw new \LogicException('Cannot leave node: instance stack is empty');
        }

        array_pop($this->instanceStack);
        array_pop($this->pathSegments);

        $this->path = '/' . implode('/', $this->pathSegments);
        $this->path = $this->path === '/' ? '' : $this->path;
    }

    public function getCurrentPath()
    {
        return $this->path;
    }

    public function addViolation($message, array $parameters = [])
    {
        $this->violations[] = [
            'path' => $this->path,
            'message' => vsprintf($message, $parameters)
        ];
    }

    public function getViolations()
    {
        return $this->violations;
    }

    public function getVersion()
    {
        return $this->version;
    }


    // versions should be stack-able...


    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function duplicate()
    {
        // ok as long as context doesn't hold object references

        return clone $this;
    }

    public function mergeViolations(Context $context)
    {
        $this->violations = array_merge($this->violations, $context->getViolations());
    }
}
