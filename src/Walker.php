<?php

namespace JsonSchema;

use stdClass;

class Walker
{
    private $registry;
    private $resolver;

    private $parsedSchemas = [];

    public function __construct(Registry $registry, Resolver $resolver)
    {
        $this->registry = $registry;
        $this->resolver = $resolver;
    }

    // resolve, normalize and validate schema
    public function parseSchema(stdClass $schema, Context $context)
    {
        foreach ($this->parsedSchemas as $parsedSchema) {
            if ($schema === $parsedSchema || Utils::areEqual($schema, $parsedSchema)) {
                return $schema;
            }
        }

        $this->parsedSchemas[] = $schema;

        if (!$this->resolver->hasBaseSchema()) {
            $this->resolver->setBaseSchema($schema, '');
        }

        $isBaseSchema = $schema === $this->resolver->getBaseSchema();

        if (property_exists($schema, '$ref')) {
            do {
                $resolved = $this->resolver->resolve($schema);
                $ancestor = $this->resolver->getBaseSchema();
                $this->resolver->replaceInAncestor($schema, $resolved, $ancestor);
                $schema = $resolved;
            } while (property_exists($resolved, '$ref'));

            if ($isBaseSchema) {
                $this->resolver->setBaseSchema($schema, $this->resolver->getBaseUri());
            }
        }

        $this->loadConstraints($schema, $context);

        if (property_exists($schema, 'id')) {
            // alter scope
        }

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

    private function loadConstraints(stdClass $schema, Context $context)
    {
        if (property_exists($schema, '$schema')) {
            $context->setVersion($schema->{'$schema'});
        }

        $this->registry->loadConstraintsFor($context->getVersion());
    }
}

