<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal;

use stdClass;

/**
 * Implements the three steps needed to perform a JSON Schema validation,
 * i.e. distinct methods to recursively:.
 *
 * 1) resolve JSON pointer references within schema
 * 2) normalize and validate the syntax of the schema
 * 3) validate a given instance against it
 */
class Walker
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var array
     */
    private $parsedSchemas = [];

    /**
     * @var array
     */
    private $resolvedSchemas = [];

    /**
     * @var Constraint[][]
     */
    private $constraintsCache = [];

    /**
     * Constructor.
     *
     * @param Registry $registry
     * @param Resolver $resolver
     */
    public function __construct(Registry $registry, Resolver $resolver)
    {
        $this->registry = $registry;
        $this->resolver = $resolver;
    }

    /**
     * Recursively resolve JSON pointer references within a given schema.
     *
     * @param stdClass $schema The schema to resolve
     * @param Uri      $uri    The URI of the schema
     *
     * @return stdClass
     */
    public function resolveReferences(stdClass $schema, Uri $uri)
    {
        $this->resolver->initialize($schema, $uri);

        return $this->doResolveReferences($schema, $uri);
    }

    /**
     * @param stdClass $schema
     * @param Uri      $uri
     *
     * @return stdClass
     */
    private function doResolveReferences(stdClass $schema, Uri $uri)
    {
        if ($this->isProcessed($schema, $this->resolvedSchemas)) {
            return $schema;
        }

        $inScope = false;

        if (property_exists($schema, 'id') && is_string($schema->id)) {
            $this->resolver->enter(new Uri($schema->id));
            $inScope = true;
        }

        if (property_exists($schema, '$ref')) {
            $resolved = $this->resolver->resolve($schema);
            $this->resolver->enter($resolved[0], $resolved[1]);
            $schema = $this->doResolveReferences($resolved[1], $resolved[0]);
            $this->resolver->leave();
        } else {
            foreach ($schema as $property => $value) {
                if (is_object($value)) {
                    $schema->{$property} = $this->doResolveReferences($value, $uri);
                } elseif (is_array($value)) {
                    foreach ($value as $index => $element) {
                        if (is_object($element)) {
                            $schema->{$property}[$index] = $this->doResolveReferences($element, $uri);
                        }
                    }
                }
            }
        }

        if ($inScope) {
            $this->resolver->leave();
        }

        return $schema;
    }

    /**
     * Recursively normalizes a given schema and validates its syntax.
     *
     * @param stdClass $schema
     * @param Context  $context
     *
     * @return stdClass
     */
    public function parseSchema(stdClass $schema, Context $context)
    {
        if ($this->isProcessed($schema, $this->parsedSchemas)) {
            return $schema;
        }

        $version = $this->getVersion($schema);
        $constraints = $this->registry->getConstraints($version);
        $constraints = $this->filterConstraintsForSchema($constraints, $schema);

        foreach ($constraints as $constraint) {
            $constraint->normalize($schema, $context, $this);
        }

        return $schema;
    }

    /**
     * Validates an instance against a given schema, populating a context
     * with encountered violations.
     *
     * @param mixed    $instance
     * @param stdClass $schema
     * @param Context  $context
     */
    public function applyConstraints($instance, stdClass $schema, Context $context)
    {
        $cacheKey = gettype($instance).spl_object_hash($schema);
        $constraints = & $this->constraintsCache[$cacheKey];

        if ($constraints === null) {
            $version = $this->getVersion($schema);
            $instanceType = Types::getPrimitiveTypeOf($instance);
            $constraints = $this->registry->getConstraintsForType($version, $instanceType);
            $constraints = $this->filterConstraintsForSchema($constraints, $schema);
        }

        foreach ($constraints as $constraint) {
            $constraint->apply($instance, $schema, $context, $this);
        }
    }

    /**
     * Returns whether a schema has already been processed and stored in
     * a given collection. This is helpful both as a cache lookup and as
     * an infinite recursion check.
     *
     * @param stdClass $schema
     * @param array    $processed
     *
     * @return bool
     */
    private function isProcessed(stdClass $schema, array &$processed)
    {
        $schemaHash = spl_object_hash($schema);

        if (isset($processed[$schemaHash])) {
            return true;
        }

        $processed[$schemaHash] = true;

        return false;
    }

    /**
     * Returns the version of a schema.
     *
     * @param stdClass $schema
     *
     * @return string
     */
    private function getVersion(stdClass $schema)
    {
        return property_exists($schema, '$schema') ?
            $schema->{'$schema'} :
            Registry::VERSION_CURRENT;
    }

    /**
     * Filters constraints which should be triggered for given schema.
     *
     * @param Constraint[] $constraints
     * @param stdClass     $schema
     *
     * @return Constraint[]
     */
    private function filterConstraintsForSchema(array $constraints, stdClass $schema)
    {
        $filtered = [];

        foreach ($constraints as $constraint) {
            foreach ($constraint->keywords() as $keyword) {
                if (property_exists($schema, $keyword)) {
                    $filtered[] = $constraint;
                    break;
                }
            }
        }

        return $filtered;
    }
}
