<?php

namespace JsonSchema;

use stdClass;

class Walker
{
    private $registry;

    public function __construct(Registry $registry, Resolver $resolver)
    {
        $this->registry = $registry;
    }

    // resolve, normalize and validate schema
    public function parseSchema(stdClass $schema, Context $context)
    {
        if (property_exists($schema, '$ref')) {
            // if local ref ()

            // if pointer in registry, get schema from there
            // else resolve ref

            // remove all schema attributes and add ones from retrieved schema
            // recursive call to walk with the schema

            // store schema by pointer in registry (recursion ?)
        } else {
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
        }
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

