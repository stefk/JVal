<?php

namespace JsonSchema;

use JsonSchema\Constraint;
use stdClass;

class Validator
{
    private $walker;

    public static function buildDefault(\Closure $resolveHook = null)
    {
        $registry = new Registry();
        $resolver = new Resolver();

        if ($resolveHook) {
            $resolver->setResolveHook($resolveHook);
        }

        $walker = new Walker($registry, $resolver);

        return new Validator($walker);
    }

    public function __construct(Walker $walker)
    {
        $this->walker = $walker;
    }

    public function validate($instance, stdClass $schema)
    {
        $parseContext = new Context();
        $constraintContext = new Context();

//        var_dump('validating', $instance, 'with', $schema);

        $schema = $this->walker->parseSchema($schema, $parseContext);

//        var_dump('schema after parsing', $schema);

        // todo: keep ref of already parsed schemas

        $this->walker->applyConstraints($instance, $schema, $constraintContext);

        return $constraintContext->getViolations();
    }
}
