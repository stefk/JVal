<?php

namespace JsonSchema;

use JsonSchema\Exception\ResolverException;
use stdClass;

class Resolver
{
    private $schemas = [];
    private $baseUri;
    private $baseSchema;

    /**
     * Sets the current schema, on which resolutions will be based.
     *
     * @param stdClass  $schema
     * @param string    $uri
     * @throws ResolverException
     */
    public function setBaseSchema(stdClass $schema, $uri)
    {
        $this->registerSchema($schema, $uri);
        $this->baseUri = $uri;
        $this->baseSchema = $schema;
    }

    /**
     * Returns the current base schema.
     *
     * @return stdClass
     * @throws ResolverException
     */
    public function getBaseSchema()
    {
        if (!isset($this->baseSchema)) {
            throw new ResolverException(
                'No base schema has been set',
                ResolverException::NO_BASE_SCHEMA
            );
        }

        return $this->baseSchema;
    }

    /**
     * Resolves a schema reference according to the JSON Reference
     * specification draft.
     *
     * @param stdClass $reference
     * @return stdClass
     * @throws ResolverException
     */
    public function resolve(stdClass $reference)
    {
        $pointerUri = rawurldecode($reference->{'$ref'});
        $uriParts = explode('#', $pointerUri);
        $uri = $uriParts[0];
        $pointer = isset($uriParts[1]) ? $uriParts[1] : '';
        $baseSchema = $this->getBaseSchema();

        if ($uri !== '' && $uri !== $this->baseUri) {
            $baseSchema = isset($this->schemas[$uri]) ?
                $this->schemas[$uri] :
                $this->fetchSchemaAt($uriParts[0]);
            $this->registerSchema($baseSchema, $uriParts[0]);
        }

        $resolved = $this->resolvePointer($baseSchema, $pointer);

        if ($resolved === $reference) {
            throw new ResolverException(
               'Pointer self reference detected',
               ResolverException::SELF_REFERENCING_POINTER
            );
        }

        if (!is_object($resolved)) {
            throw new ResolverException(
                "Target of pointer '{$pointerUri}' is not a valid schema",
                ResolverException::INVALID_POINTER_TARGET
            );
        }

        return $resolved;
    }

    /**
     * Caches a schema reference for future use.
     *
     * @param stdClass  $schema
     * @param string    $uri
     */
    private function registerSchema(stdClass $schema, $uri)
    {
        if (!isset($this->schemas[$uri])) {
            $this->schemas[$uri] = $schema;
        }
    }

    /**
     * Fetches a remote schema and ensures it is valid.
     *
     * @param string $uri
     * @return stdClass
     * @throws ResolverException
     */
    private function fetchSchemaAt($uri)
    {
        set_error_handler(function ($severity, $error) use ($uri) {
            $message = 'Failed to fetch URI "%s" (error: "%s", severity: %s)';
            restore_error_handler();

            throw new ResolverException(
                sprintf($message, $uri, $error, $severity),
                ResolverException::UNFETCHABLE_URI
            );
        });

        $content = file_get_contents($uri);
        restore_error_handler();

        $schema = json_decode($content);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $message = 'Cannot decode JSON from URI "%s" (error: %s)';

            throw new ResolverException(
                sprintf($message, $uri, json_last_error_msg()),
                ResolverException::JSON_DECODE_ERROR
            );
        }

        if (!is_object($schema)) {
            throw new ResolverException(
                "Content fetched at '{$uri}' is not a valid schema",
                ResolverException::INVALID_REMOTE_SCHEMA
            );
        }

        return $schema;
    }

    /**
     * Resolves a JSON pointer according to RFC 6901.
     *
     * @param stdClass  $schema
     * @param string    $pointer
     * @return mixed
     * @throws ResolverException
     */
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
