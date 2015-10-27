<?php

namespace JsonSchema;

use stdClass;

class Walker
{
    private $registry;
    private $resolver;
    private $parsedSchemas = [];
    private $resolvedSchemas = [];

    public function __construct(Registry $registry, Resolver $resolver)
    {
        $this->registry = $registry;
        $this->resolver = $resolver;
    }

    public function resolveReferences(stdClass $schema, Uri $uri)
    {
        if ($this->isLooping($schema, $this->resolvedSchemas)) {
            return $schema;
        }

        if (!$this->resolver->hasBaseSchema()) {
            $this->resolver->setBaseSchema($schema, $uri);
        }

        $inScope = false;

        if (property_exists($schema, 'id') && is_string($schema->id)) {
            $this->resolver->enter(new Uri($schema->id));
            $inScope = true;
        }

        if (property_exists($schema, '$ref')) {
            $resolved = $this->resolver->resolve($schema);
            $this->resolver->enter($resolved[0], $resolved[1]);
            $schema = $this->resolveReferences($resolved[1], $resolved[0]);
            $this->resolver->leave();
        } else {
            foreach ($schema as $property => $value) {
                if (is_object($value)) {
                    $schema->{$property} = $this->resolveReferences($value, $uri);
                } elseif (is_array($value)) {
                    foreach ($value as $index => $element) {
                        if (is_object($element)) {
                            $schema->{$property}[$index] = $this->resolveReferences($element, $uri);
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

    public function parseSchema(stdClass $schema, Context $context)
    {
        if ($this->isLooping($schema, $this->parsedSchemas)) {
            return $schema;
        }

        $this->loadConstraints($schema, $context);

        foreach ($this->registry->getConstraints() as $constraint) {
            foreach ($constraint->keywords() as $keyword) {
                if (property_exists($schema, $keyword)) {
                    $constraint->normalize($schema, $context, $this);
                    break;
                }
            }
        }

        return $schema;
    }

    public function applyConstraints($instance, stdClass $schema, Context $context)
    {
        $this->loadConstraints($schema, $context);
        $instanceType = Types::getPrimitiveTypeOf($instance);

        foreach ($this->registry->getConstraints() as $constraint) {
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

    private function loadConstraints(stdClass $schema, Context $context)
    {
        if (property_exists($schema, '$schema')) {
            $context->setVersion($schema->{'$schema'});
        }

        $this->registry->loadConstraintsFor($context->getVersion());
    }
}

