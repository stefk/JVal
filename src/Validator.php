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
        $context = new Context();

        return $this->doValidate($instance, $schema, $context);
    }

    private function doValidate($instance, stdClass $schema, Context $context)
    {
        $this->walker->walk($instance, $schema, $context);

        return $context->getViolations();
    }
}
