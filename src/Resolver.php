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

    private $currentUri;

    private $uriStack = [];
    private $scopeStack = [];

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
     * @param Uri       $uri
     */
    public function setBaseSchema(stdClass $schema, Uri $uri)
    {
        $this->registerSchema($schema, $uri);
        $this->baseUri = new $uri;
        $this->baseSchema = $schema;
        $this->uriStack = [$this->baseUri];
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
     * Returns the URI of the current schema.
     *
     * @return Uri
     */
    public function getCurrentUri()
    {
        if (count($this->uriStack) === 0) {
            throw new \Exception('URI stack is empty');
        }

        return end($this->uriStack);
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


    public function enterScope(stdClass $schema)
    {

    }

    public function leaveScope()
    {
        if (count($this->scopeStack[$this->currentUri]) === 0) {
            throw new \Exception('Cannot leave scope: stack for current URI is empty');
        }

        array_pop($this->scopeStack);
    }

    public function leaveUri()
    {

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
    public function resolve(Uri $uri)
    {
        $currentUri = $this->getCurrentUri();
        $this->uriStack[] = $uri;

        if (!$currentUri->isSamePrimaryResource($uri)) {
            $identifier = $uri->getPrimaryResourceIdentifier();
            $schema = isset($this->schemas[$identifier]) ?
                $this->schemas[$identifier] :
                $this->fetchSchemaAt($uri->getRawUri());
            $this->registerSchema($schema, $identifier);
        }

//        if ($hook = $this->resolveHook) {
//            $pointerUri = $hook($pointerUri);
//        }

//        $pointerUri = rawurldecode($pointerUri);
//        $uriParts = explode('#', $pointerUri);
//        $uri = $uriParts[0];
//        $pointer = isset($uriParts[1]) ? $uriParts[1] : '';
//        $baseSchema = $this->getBaseSchema();
//
//        if ($uri !== '' && $uri !== $this->baseUri) {
//            $baseSchema = isset($this->schemas[$uri]) ?
//                $this->schemas[$uri] :
//                $this->fetchSchemaAt($uriParts[0]);
//            $this->registerSchema($baseSchema, $uriParts[0]);
//        }

        //var_dump([$baseSchema, $pointer]);

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
        $this->doReplaceInAncestor($subSchema, $replacementSchema, $ancestor, []);
    }

    public function doReplaceInAncestor(
        stdClass $subSchema,
        stdClass $replacementSchema,
        stdClass $ancestor,
        array $stack
    )
    {
        if (in_array($ancestor, $stack)) {
            return;
        }

        $stack[] = $ancestor;

        foreach ($ancestor as $property => $value) {
            if (Utils::areEqual($value, $subSchema)) {
                $ancestor->{$property} = $replacementSchema;
            } elseif (is_object($value)) {
                $this->doReplaceInAncestor($subSchema, $replacementSchema, $value, $stack);
            } elseif (is_array($value)) {
                foreach ($value as $index => $element) {
                    if (Utils::areEqual($element, $subSchema)) {
                        $ancestor->{$property}[$index] = $replacementSchema;
                    } elseif (is_object($element)) {
                        $this->doReplaceInAncestor($subSchema, $replacementSchema, $element, $stack);
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
