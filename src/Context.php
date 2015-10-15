<?php

namespace JsonSchema;

class Context
{
    private $violations = [];
    private $path = '';
    private $instance;

    public function setNode($instance, $path)
    {
        $this->instance = $instance;
        $this->path = $path;
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

    public function getDefaultVersion()
    {
        return Registry::VERSION_DRAFT_4;
    }
}
