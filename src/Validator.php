<?php

namespace JVal;

use JVal\Constraint;
use stdClass;

class Validator
{
    private $walker;

    public static function buildDefault(\Closure $preFetchHook = null)
    {
        $registry = new Registry();
        $resolver = new Resolver();

        if ($preFetchHook) {
            $resolver->setPreFetchHook($preFetchHook);
        }

        $walker = new Walker($registry, $resolver);

        return new Validator($walker);
    }

    public function __construct(Walker $walker)
    {
        $this->walker = $walker;
    }

    public function validate($instance, stdClass $schema, $schemaUri = '')
    {
        $parseContext = new Context();
        $constraintContext = new Context();

        // todo: keep ref of already resolved/parsed schemas

        $schema = $this->walker->resolveReferences($schema, new Uri($schemaUri));
        $schema = $this->walker->parseSchema($schema, $parseContext);
        $this->walker->applyConstraints($instance, $schema, $constraintContext);

        return $constraintContext->getViolations();
    }
}
