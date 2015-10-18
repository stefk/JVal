<?php

namespace JsonSchema;

use JsonSchema\Constraint;
use stdClass;

class Validator
{
    private $walker;

    public static function buildDefault()
    {
        $registry = new Registry();
        $resolver = new Resolver();
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

        $this->walker->parseSchema($schema, $parseContext);

        // todo: keep ref of already parsed schemas

        $this->walker->applyConstraints($instance, $schema, $constraintContext);

        return $constraintContext->getViolations();
    }
}
