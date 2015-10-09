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

    public function addViolation($message)
    {
        $this->violations[] = [
            'path' => $this->path,
            'message' => $message
        ];
    }

    public function getViolations()
    {
        return $this->violations;
    }
}
