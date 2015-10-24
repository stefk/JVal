<?php

namespace JsonSchema;

use JsonSchema\Exception\Resolver\InvalidPointerIndexException;
use JsonSchema\Exception\Resolver\InvalidPointerTargetException;
use JsonSchema\Exception\Resolver\InvalidRemoteSchemaException;
use JsonSchema\Exception\Resolver\InvalidSegmentTypeException;
use JsonSchema\Exception\Resolver\JsonDecodeErrorException;
use JsonSchema\Exception\Resolver\NoBaseSchemaException;
use JsonSchema\Exception\Resolver\SelfReferencingPointerException;
use JsonSchema\Exception\Resolver\UnfetchableUriException;
use JsonSchema\Exception\Resolver\UnresolvedPointerIndexException;
use JsonSchema\Exception\Resolver\UnresolvedPointerPropertyException;
use Closure;
use stdClass;

class Resolver
{
    private $schemas = [];
    private $baseUri;
    private $baseSchema;
    private $resolveHook;

    /**
     * Returns whether a base schema has been set.
     *
     * @return bool
     */
    public function hasBaseSchema()
    {
        return isset($this->baseSchema);
    }

    /**
     * Sets the current schema, on which resolutions will be based.
     *
     * @param stdClass  $schema
     * @param string    $uri
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
     * @throws NoBaseSchemaException
     */
    public function getBaseSchema()
    {
        if (!isset($this->baseSchema)) {
            throw new NoBaseSchemaException();
        }

        return $this->baseSchema;
    }

    /**
     * Returns the URI of the current base schema.
     *
     * @return string
     * @throws NoBaseSchemaException
     */
    public function getBaseUri()
    {
        if (!isset($this->baseSchema)) {
            throw new NoBaseSchemaException();
        }

        return $this->baseUri;
    }

    /**
     * Sets a resolve hook. The hook function will be called each time a
     * reference is resolved. It is passed the original pointer URI and must
     * return a new URI string.
     *
     * @param Closure $resolveHook
     */
    public function setResolveHook(Closure $resolveHook)
    {
        $this->resolveHook = $resolveHook;
    }

    /**
     * Resolves a schema reference according to the JSON Reference
     * specification draft.
     *
     * @param stdClass $reference
     * @throws InvalidPointerTargetException
     * @throws NoBaseSchemaException
     * @throws SelfReferencingPointerException
     * @return stdClass
     */
    public function resolve(stdClass $reference)
    {
        $pointerUri = $reference->{'$ref'};

        if ($hook = $this->resolveHook) {
            $pointerUri = $hook($pointerUri);
        }

        $pointerUri = rawurldecode($pointerUri);
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
            throw new SelfReferencingPointerException();
        }

        if (!is_object($resolved)) {
            throw new InvalidPointerTargetException([$pointerUri]);
        }

        return $resolved;
    }

    /**
     * Recursively searches occurrences of a subSchema in an ancestor schema,
     * and replaces them by references to another schema.
     *
     * @param stdClass $subSchema
     * @param stdClass $replacementSchema
     * @param stdClass $ancestor
     */
    public function replaceInAncestor(
        stdClass $subSchema,
        stdClass $replacementSchema,
        stdClass $ancestor
    )
    {
        foreach ($ancestor as $property => $value) {
            if ($value === $subSchema) {
                $ancestor->{$property} = $replacementSchema;
            } elseif (is_object($value)) {
                $this->replaceInAncestor($subSchema, $replacementSchema, $value);
            } elseif (is_array($value)) {
                foreach ($value as $index => $element) {
                    if ($element === $subSchema) {
                        $ancestor->{$property}[$index] = $replacementSchema;
                    } elseif (is_object($element)) {
                        $this->replaceInAncestor($subSchema, $replacementSchema, $element);
                    }
                }
            }
        }
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
     * @throws InvalidRemoteSchemaException
     * @throws JsonDecodeErrorException
     * @return stdClass
     */
    private function fetchSchemaAt($uri)
    {
        set_error_handler(function ($severity, $error) use ($uri) {
            restore_error_handler();
            throw new UnfetchableUriException([$uri, $error, $severity]);
        });

        $content = file_get_contents($uri);
        restore_error_handler();

        $schema = json_decode($content);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonDecodeErrorException([$uri, json_last_error_msg()]);
        }

        if (!is_object($schema)) {
            throw new InvalidRemoteSchemaException([$uri]);
        }

        return $schema;
    }

    /**
     * Resolves a JSON pointer according to RFC 6901.
     *
     * @param stdClass $schema
     * @param string $pointer
     * @return mixed
     * @throws InvalidPointerIndexException
     * @throws InvalidSegmentTypeException
     * @throws UnresolvedPointerIndexException
     * @throws UnresolvedPointerPropertyException
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
                if (property_exists($currentNode, $segments[$i])) {
                    $currentNode = $currentNode->{$segments[$i]};
                    continue;
                }

                throw new UnresolvedPointerPropertyException([$segments[$i], $i, $pointer]);
            }

            if (is_array($currentNode)) {
                if (!preg_match('/^\d+$/', $segments[$i])) {
                    throw new InvalidPointerIndexException([$segments[$i], $i, $pointer]);
                }

                if (!isset($currentNode[$index = (int) $segments[$i]])) {
                    throw new UnresolvedPointerIndexException([$segments[$i], $i, $pointer]);
                }

                $currentNode = $currentNode[$index];
                continue;
            }

            throw new InvalidSegmentTypeException([$i, $pointer]);
        }

        return $currentNode;
    }
}
