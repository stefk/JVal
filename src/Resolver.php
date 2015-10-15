<?php

namespace JsonSchema;

use JsonSchema\Exception\ResolverException;
use stdClass;

class Resolver
{
    private $schemas = [];
    private $stack = [];

    public function pushSchema(stdClass $schema, $uri)
    {
        if (isset($this->schemas[$uri])) {
            throw new ResolverException(
                "A schema is already registered for uri '{$uri}'",
                ResolverException::ALREADY_REGISTERED_URI
            );
        }

        $this->schemas[$uri] = $schema;
        $this->stack[] = $schema;
    }

    public function popSchema()
    {
        if (count($this->stack) === 0) {
            throw new ResolverException(
                'The schema stack is empty',
                ResolverException::EMPTY_SCHEMA_STACK
            );
        }

        return array_pop($this->stack);
    }

    public function currentSchema()
    {
        if (count($this->stack) === 0) {
            throw new ResolverException(
                'The schema stack is empty',
                ResolverException::EMPTY_SCHEMA_STACK
            );
        }

        return $this->stack[count($this->stack) - 1];
    }

    public function resolve(stdClass $reference)
    {
        $pointerUri = $reference->{'$ref'};
        $pointerUri = rawurldecode($pointerUri);

        if (0 === strpos($pointerUri, '#')) {
            $resolved = $this->resolvePointer(
                $this->currentSchema(),
                strlen($pointerUri) > 1 ? substr($pointerUri, 1) : ''
            );

            if ($resolved === $reference) {
                throw new ResolverException(
                   'Pointer self reference detected',
                   ResolverException::SELF_REFERENCING_POINTER
                );
            }
        } else {
            $resolved = 'REMOTE REF NOT IMPLEMENTED';
            //throw new \Exception('Remote refs not implemented');
        }

        if (!is_object($resolved)) {
            throw new ResolverException(
                "Target of pointer '{$pointerUri}' is not a valid schema",
                ResolverException::INVALID_POINTER_TARGET
            );
        }

        return $resolved;
    }

    private function resolvePointer(stdClass $schema, $pointer)
    {
        $segments = explode('/', $pointer);
        $currentNode = $schema;

        for ($i = 0, $max = count($segments); $i < $max; ++$i) {
            if ($segments[$i] === '') {
                continue;
            }

            $segments[$i] = str_replace('~1', '/', $segments[$i]);
            $segments[$i] = str_replace('~0', '~', $segments[$i]);

            if (is_object($currentNode)) {
                if (isset($currentNode->{$segments[$i]})) {
                    $currentNode = $currentNode->{$segments[$i]};
                    continue;
                }

                throw new ResolverException(
                    "Cannot resolve property '{$segments[$i]}' at position {$i} in pointer '{$pointer}'",
                    ResolverException::UNRESOLVED_POINTER_PROPERTY
                );
            }

            if (is_array($currentNode)) {
                if (!preg_match('/^\d+$/', $segments[$i])) {
                    throw new ResolverException(
                        "Invalid index '{$segments[$i]}' at position {$i} in pointer '{$pointer}'",
                        ResolverException::INVALID_POINTER_INDEX
                    );
                }

                if (!isset($currentNode[$index = (int) $segments[$i] - 1])) {
                    throw new ResolverException(
                        "Cannot resolve index '{$segments[$i]}' at position {$i} in pointer '{$pointer}'",
                        ResolverException::UNRESOLVED_POINTER_INDEX
                    );
                }

                $currentNode = $currentNode[$index];
                continue;
            }

            throw new ResolverException(
                "Invalid segment type at position {$i} in pointer '{$pointer}'",
                ResolverException::INVALID_SEGMENT_TYPE
            );
        }

        return $currentNode;
    }
}
