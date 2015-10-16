<?php

namespace JsonSchema\Testing;

abstract class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    private $exceptionClass;

    /**
     * Sets a default exception class for #expectException().
     *
     * @param string $class
     */
    protected function setExceptionClass($class)
    {
        $this->exceptionClass = $class;
    }

    /**
     * Asserts an exception of class previously set will be thrown
     * with a given code.
     *
     * @param int $code
     */
    protected function expectException($code)
    {
        $this->setExpectedException($this->exceptionClass, null, $code);
    }

    /**
     * Returns the JSON-decoded content of a file.
     *
     * @param string $file
     * @return mixed
     * @throws \Exception
     */
    protected function loadJsonFromFile($file)
    {
        if (!file_exists($file)) {
            throw new \Exception("File {$file} doesn't exist");
        }

        $content = json_decode(file_get_contents($file));

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception(sprintf(
                'json_decode error in file %s -> Error: %s',
                $file,
                json_last_error_msg()
            ));
        }

        return $content;
    }

    /**
     * Returns a JSON-decoded schema from tests/Data/schemas.
     *
     * @param string $name Name of the file without the extension
     * @return mixed
     */
    protected function loadSchema($name)
    {
        $schemaDir = realpath(__DIR__ . '/../../tests/Data/schemas');

        return $this->loadJsonFromFile("{$schemaDir}/{$name}.json");
    }
}
