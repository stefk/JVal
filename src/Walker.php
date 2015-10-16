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
            $version = isset($schema->{'$schema'}) ?
                $schema->{'$schema'} :
                $context->getDefaultVersion();
            $this->registry->loadConstraintsFor($version);


            if (isset($schema->id)) {
                // alter scope
            }

            $instanceType = $this->registry->getPrimitiveTypeOf($instance);

            foreach ($this->registry->getConstraints() as $constraint) {
                foreach ($constraint->keywords() as $keyword) {
                    if ($constraint->isApplicableTo($instanceType)) {
                        if (isset($schema->{$keyword})) {
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

