<?php

namespace JVal\Testing;

use JVal\Constraint;
use JVal\Context;
use JVal\Exception\ConstraintException;
use JVal\Registry;
use JVal\Resolver;
use JVal\Walker;

abstract class ConstraintTestCase extends BaseTestCase
{
    private $expectedExceptionClass;
    private $expectedExceptionPath;
    private $expectedExceptionTarget;

    /**
     * @dataProvider applyTestProvider
     *
     * @param string    $file
     * @param string    $title
     * @param mixed     $instance
     * @param \stdClass $schema
     * @param bool      $isInstanceValid
     * @param array     $expectedErrors
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
        $schemaContext = new Context();
        $validationContext = new Context();
        $walker = new Walker(new Registry(), new Resolver());
        $constraint->normalize($schema, $schemaContext, $walker);
        $constraint->apply($instance, $schema, $validationContext, $walker);
        $actualErrors = $validationContext->getViolations();

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

    /**
     * Returns a mocked walker instance.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|Walker
     */
    protected function mockWalker()
    {
        return $this->mock('JVal\Walker');
    }

    /**
     * Asserts a constraint exception will be thrown at a given path
     * and optionally on a given target.
     *
     * @param string $exceptionName
     * @param string $path
     * @param string $target
     */
    protected function expectConstraintException($exceptionName, $path, $target = null)
    {
        $this->expectedExceptionClass = "JVal\\Exception\\Constraint\\{$exceptionName}";
        $this->expectedExceptionPath = $path;
        $this->expectedExceptionTarget = $target;
        $this->expectException();
    }

    /**
     * Implements the default hook, asserting that the exception thrown
     * is an instance of ConstraintException and that its path and target
     * match the expectations.
     *
     * @param \Exception $ex
     * @throws \Exception
     */
    protected function exceptionHook(\Exception $ex)
    {
        if (empty($this->expectedExceptionClass)) {
            throw $ex;
        }

        $this->assertThat(
            $ex,
            new \PHPUnit_Framework_Constraint_Exception(
                $this->expectedExceptionClass
            )
        );

        if (!$ex instanceof ConstraintException) {
            $this->fail('Exception thrown is not a ConstraintException');
        }

        $this->assertEquals(
            $this->expectedExceptionPath,
            $ex->getPath(),
            'Exception was not thrown at the expected path.'
        );

        if (!empty($this->expectedExceptionTarget)) {
            $this->assertEquals(
                $this->expectedExceptionTarget,
                $ex->getTarget(),
                'Exception doesn not have the expected target.'
            );
        }
    }
}
