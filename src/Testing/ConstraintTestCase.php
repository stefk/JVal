<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal\Testing;

use JVal\Constraint;
use JVal\Context;
use JVal\Exception\ConstraintException;
use JVal\Registry;
use JVal\Resolver;
use JVal\Walker;

/**
 * Test case for testing validation constraints.
 */
abstract class ConstraintTestCase extends DataTestCase
{
    private $expectedExceptionClass;
    private $expectedExceptionPath;
    private $expectedExceptionTarget;

    /**
     * @dataProvider fileDataProvider
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
    ) {
        $constraint = $this->getConstraint();
        $schemaContext = new Context();
        $validationContext = new Context();
        $walker = new Walker(new Registry(), new Resolver());

        $pathBefore = $schemaContext->getCurrentPath();
        $constraint->normalize($schema, $schemaContext, $walker);
        $this->assertSame($pathBefore, $schemaContext->getCurrentPath());

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
     * Returns an instance of the constraint to be tested.
     *
     * @return Constraint
     */
    abstract protected function getConstraint();

    /**
     * @codeCoverageIgnore (called from a data provider, before test execution)
     *
     * {@inheritDoc}
     */
    protected function getDataDirectory()
    {
        return __DIR__.'/../../tests/Data/constraints';
    }

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
     *
     * @throws \Exception
     */
    protected function exceptionHook(\Exception $ex)
    {
        // @codeCoverageIgnoreStart
        if (empty($this->expectedExceptionClass)) {
            throw $ex;
        }
        // @codeCoverageIgnoreEnd

        $this->assertThat(
            $ex,
            new \PHPUnit_Framework_Constraint_Exception(
                $this->expectedExceptionClass
            )
        );

        if ($ex instanceof ConstraintException) {
            $this->assertEquals(
                $this->expectedExceptionPath,
                $ex->getPath(),
                'Exception was not thrown at the expected path.'
            );

            if (!empty($this->expectedExceptionTarget)) {
                $this->assertEquals(
                    $this->expectedExceptionTarget,
                    $ex->getTarget(),
                    'Exception does not have the expected target.'
                );
            }

            return;
        }

        // @codeCoverageIgnoreStart
        $this->fail('Exception thrown is not a ConstraintException');
        // @codeCoverageIgnoreEnd
    }
}
