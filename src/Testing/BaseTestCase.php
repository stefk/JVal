<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal\Testing;

use JVal\Utils;

/**
 * Provides common methods for dealing with JSON data (loading, assertions,
 * etc.).
 */
abstract class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    private $expectException = false;

    /**
     * Wraps the default #runTest() method to provide an exception hook.
     *
     * @return mixed|null
     *
     * @throws \Exception
     */
    protected function runTest()
    {
        try {
            $result = parent::runTest();
        } catch (\Exception $ex) {
            $this->exceptionHook($ex);

            return;
        }

        // @codeCoverageIgnoreStart
        if ($this->expectException) {
            $this->fail('An exception was expected but none has been thrown.');
        }
        // @codeCoverageIgnoreEnd

        return $result;
    }

    /**
     * Sets the flag indicating that an exception is expected.
     */
    protected function expectException()
    {
        $this->expectException = true;
    }

    /**
     * @codeCoverageIgnore (shouldn't happen in a green test suite)
     *
     * Hook called when an unexpected exception is thrown.
     *
     * Override this hook to make custom assertions on exceptions.
     *
     * @param \Exception $ex
     *
     * @throws \Exception
     */
    protected function exceptionHook(\Exception $ex)
    {
        throw $ex;
    }

    /**
     * Returns the JSON-decoded content of a file.
     *
     * @param string $file
     *
     * @return mixed
     */
    protected function loadJsonFromFile($file)
    {
        return Utils::loadJsonFromFile($file);
    }

    /**
     * Returns a JSON-decoded schema from tests/Data/schemas.
     *
     * @param string $name Name of the file without the extension
     *
     * @return mixed
     */
    protected function loadSchema($name)
    {
        $schemaDir = realpath(__DIR__.'/../../tests/Data/schemas');

        return $this->loadJsonFromFile("{$schemaDir}/{$name}.json");
    }

    /**
     * Asserts the validation results equal the expected one and make
     * a full report otherwise.
     *
     * @param string    $file
     * @param string    $title
     * @param mixed     $instance
     * @param \stdClass $schema
     * @param bool      $isInstanceValid
     * @param array     $expectedErrors
     * @param array     $actualErrors
     */
    protected function assertValidationResult(
        $file,
        $title,
        $instance,
        \stdClass $schema,
        $isInstanceValid,
        array $expectedErrors,
        array $actualErrors
    ) {
        $reportParameters = array(
            $file,
            $title,
            $this->dump($schema),
            $this->dump($instance),
            count($expectedErrors) > 0 ? $this->dump($expectedErrors) : 'no error',
            count($actualErrors) > 0 ? $this->dump($actualErrors) : 'no error',
        );

        if (!$isInstanceValid && count($expectedErrors) === 0) {
            $reportParameters[4] = 'at least one error';
            $this->assertHasError($actualErrors, $reportParameters);
        } else {
            $this->assertErrorsAreEqual($expectedErrors, $actualErrors, $reportParameters);
        }
    }

    /**
     * Returns a mock object, bypassing original constructor.
     *
     * @param string $class
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function mock($class)
    {
        return $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns URI to local file system.
     *
     * @param  string $path
     *
     * @return string
     */
    protected function getLocalUri($path)
    {
        // @codeCoverageIgnoreStart
        if (preg_match('/^[A-Z]:/', $path)) {
            $path = '/' . strtr($path, '\\', '/');
        }
        // @codeCoverageIgnoreEnd

        return 'file://' . $path;
    }

    /**
     * @codeCoverageIgnore (cannot cover code whose behaviour depends on PHP version)
     *
     * @param mixed $variable
     *
     * @return string
     */
    private function dump($variable)
    {
        if (defined('JSON_PRETTY_PRINT')) {
            $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;

            if (defined('JSON_PRESERVE_ZERO_FRACTION')) {
                $options |= JSON_PRESERVE_ZERO_FRACTION;
            }

            $output = @json_encode($variable, $options);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $output;
            }
        }

        return print_r($variable, true);
    }

    private function assertHasError(array $errors, array $reportParameters)
    {
        // @codeCoverageIgnoreStart
        if (count($errors) === 0) {
            $this->fail(vsprintf($this->getFailureReportMask(), $reportParameters));
        }
        // @codeCoverageIgnoreEnd
    }

    private function assertErrorsAreEqual(array $actual, array $expected, array $reportParameters)
    {
        $report = vsprintf($this->getFailureReportMask(), $reportParameters);

        // @codeCoverageIgnoreStart
        if (count($actual) !== count($expected)) {
            $this->fail($report);
        }
        // @codeCoverageIgnoreEnd

        foreach ($expected as $error) {
            // @codeCoverageIgnoreStart
            if (!in_array($error, $actual)) {
                $this->fail($report);
            }
            // @codeCoverageIgnoreEnd
        }
    }

    private function getFailureReportMask()
    {
        return <<<MSG
**********************************************************************

File: %s

Test: %s

Schema: %s

Instance: %s

Expected: %s

Actual: %s

**********************************************************************
MSG;
    }
}
