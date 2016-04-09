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
     * @var stdClass[]
     */
    private $parsedSchemas = [];

    /**
     * @var stdClass[]
     */
    private $resolvedSchemas = [];

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
        $this->resolver->setBaseSchema($schema, $uri);
        $schema = $this->doResolveReferences($schema, $uri);
        $this->resolver->clearStack();

        return $schema;
    }

    /**
     * @param stdClass $schema
     * @param Uri      $uri
     *
     * @return stdClass
     */
    private function doResolveReferences(stdClass $schema, Uri $uri)
    {
        if ($this->isLooping($schema, $this->resolvedSchemas)) {
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
        if ($this->isLooping($schema, $this->parsedSchemas)) {
            return $schema;
        }

        foreach ($this->getConstraints($schema, $context) as $constraint) {
            foreach ($constraint->keywords() as $keyword) {
                if (property_exists($schema, $keyword)) {
                    $constraint->normalize($schema, $context, $this);
                    break;
                }
            }
        }

        return $schema;
    }

    /**
     * Validates an instance against a given schema, populating a context
     * with encountered violations.
     *
     * @param $instance
     * @param stdClass $schema
     * @param Context  $context
     */
    public function applyConstraints($instance, stdClass $schema, Context $context)
    {
        $instanceType = Types::getPrimitiveTypeOf($instance);

        foreach ($this->getConstraints($schema, $context) as $constraint) {
            foreach ($constraint->keywords() as $keyword) {
                if ($constraint->supports($instanceType)) {
                    if (property_exists($schema, $keyword)) {
                        $constraint->apply($instance, $schema, $context, $this);
                        break;
                    }
                }
            }
        }
    }

    private function isLooping($item, array &$stack)
    {
        $isKnown = false;

        foreach ($stack as $knownItem) {
            if ($item === $knownItem) {
                $isKnown = true;
                break;
            }
        }

        if (!$isKnown) {
            $stack[] = $item;
        }

        return $isKnown;
    }

    private function getConstraints(stdClass $schema, Context $context)
    {
        if (property_exists($schema, '$schema')) {
            $context->setVersion($schema->{'$schema'});
        }

        return $this->registry->getConstraints($context->getVersion());
    }
}
