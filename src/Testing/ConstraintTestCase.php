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
    ) {
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
     * @codeCoverageIgnore (data provider is executed before test is launched)
     *
     * Provider of #testApply().
     */
    public function applyTestProvider()
    {
        $caseDir = realpath(__DIR__.'/../../tests/Data/cases');
        $tests = [];

        foreach ($this->getCaseFileNames() as $caseName) {
            $caseFile = "{$caseDir}/{$caseName}.json";
            $case = $this->loadJsonFromFile($caseFile);

            foreach ($case->tests as $test) {
                if (!isset($test->valid) && !isset($test->invalid)) {
                    throw new \Exception(sprintf(
                        'Test case "%s %s" has neither "valid" or "invalid" data (file: %s)',
                        $case->title,
                        $test->title,
                        $caseFile
                    ));
                }

                if (isset($test->valid)) {
                    foreach ($test->valid as $i => $instance) {
                        $tests[] = [
                            $caseFile,
                            "{$case->title} {$test->title}, valid instance #{$i}",
                            $instance,
                            $test->schema,
                            true,
                            [],
                        ];
                    }
                }

                if (isset($test->invalid)) {
                    foreach ($test->invalid as $i => $set) {
                        if (!isset($set->violations)) {
                            throw new \Exception(sprintf(
                                'Invalid test must have a "violations" property in %s',
                                $caseFile
                            ));
                        }

                        $tests[] = [
                            $caseFile,
                            "{$case->title} {$test->title}, invalid instance #{$i}",
                            $set->instance,
                            $test->schema,
                            false,
                            array_map(function ($violation) {
                                return (array) $violation;
                            }, $set->violations),
                        ];
                    }
                }
            }
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
