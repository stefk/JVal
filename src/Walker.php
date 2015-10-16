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

    public function walk($instance, stdClass $schema, Context $context)
    {
        if (isset($schema->{'$ref'})) {
            // if local ref ()

            // if pointer in registry, get schema from there
            // else resolve ref
            
            // remove all schema attributes and add ones from retrieved schema
            // recursive call to walk with the schema
            
            // store schema by pointer in registry (recursion ?)
        } else {
            if (isset($schema->{'$schema'})) {
                $context->setVersion($schema->{'$schema'});
            }

            $this->registry->loadConstraintsFor($context->getVersion());

            if (isset($schema->id)) {
                // alter scope
            }

            $instanceType = Types::getPrimitiveTypeOf($instance);

            foreach ($this->registry->getConstraints() as $constraint) {
                foreach ($constraint->keywords() as $keyword) {
                    if ($constraint->supports($instanceType)) {
                        if (isset($schema->{$keyword})) {
                            $constraint->normalize($schema);
                            $constraint->apply(
                                $instance, 
                                $schema, 
                                $context, 
                                $this
                            );
                            break;
                        }
                    }
                }
            }
        }
    }
}

