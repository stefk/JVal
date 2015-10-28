<?php

namespace JVal;

use JVal\Constraint;
use Closure;
use stdClass;

/**
 * JSON Schema validation entry point.
 */
class Validator
{
    /**
     * @var Walker
     */
    private $walker;

    /**
     * Builds a default validator instance. Accepts an optional pre-fetch
     * hook.
     *
     * @see Resolver::setPreFetchHook
     * @param Closure $preFetchHook
     * @return Validator
     */
    public static function buildDefault(Closure $preFetchHook = null)
    {
        $registry = new Registry();
        $resolver = new Resolver();

        if ($preFetchHook) {
            $resolver->setPreFetchHook($preFetchHook);
        }

        $walker = new Walker($registry, $resolver);

        return new Validator($walker);
    }

    /**
     * Constructor.
     *
     * @param Walker $walker
     */
    public function __construct(Walker $walker)
    {
        $this->walker = $walker;
    }

    /**
     * Validates an instance against a given schema and returns a list
     * of violations, if any. If the schema contains relative remote
     * references, its (absolute) URI must be passed as argument.
     *
     * @param mixed     $instance
     * @param stdClass  $schema
     * @param string    $schemaUri
     * @return array
     */
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
