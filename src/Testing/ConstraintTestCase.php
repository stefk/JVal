<?php

namespace JsonSchema\Testing;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Registry;
use JsonSchema\Resolver;
use JsonSchema\Walker;

abstract class ConstraintTestCase extends BaseTestCase
{
    protected function setUp()
    {
        $this->setExceptionClass('JsonSchema\Exception\ConstraintException');
    }

    /**
     * @dataProvider applyTestProvider
     * @param string    $file
     * @param string    $title
     * @param mixed     $instance
     * @param \stdClass $schema
     * @param bool      $isInstanceValid
     * @param array     $expectedErrors
     * @internal param string $caseName
     */
    public function testApply(
        $file,
        $title,
        $instance,
        \stdClass $schema,
        $isInstanceValid,
        array $expectedErrors
    )
    {
        $constraint = $this->getConstraint();
        $context = new Context();
        $walker = new Walker(new Registry(), new Resolver());
        $constraint->apply($instance, $schema, $context, $walker);
        $actualErrors = $context->getViolations();

        $this->assertValidationResult(
            $file,
            $title,
            $instance,
            $schema,
            $isInstanceValid,
            $expectedErrors,
            $actualErrors
        );
    }

    /**
     * Provider of #testApply().
     */
    public function applyTestProvider()
    {
        $caseDir = realpath(__DIR__ . '/../../tests/Data/cases');
        $tests = [];

        foreach ($this->getCaseFileNames() as $caseName) {
            $caseTests = $this->collectTests("{$caseDir}/{$caseName}.json");
            $tests = array_merge($tests, $caseTests);
        }

        return $tests;
    }

    /**
     * Returns an instance of the constraint to be tested.
     *
     * @return Constraint
     */
    abstract protected function getConstraint();

    /**
     * Returns an array of case file names for #testApply().
     *
     * @return array
     */
    abstract protected function getCaseFileNames();
}
