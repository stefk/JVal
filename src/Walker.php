<?php

namespace JsonSchema;

use stdClass;

class Walker
{
    private $registry;

    public function __construct(Registry $registry)
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
                

                // check schema version is supported (draft v4)
                // else throw unsupported exception
                
                // load keywords for version in registry
            } else {
                // load default keywords (from context)
            }

            foreach ($this->registry->loaded() as $constraint) {
                foreach ($constraint->keywords() as $keyword) {
                    if ($constraint->isApplicableTo($instance)) {
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

