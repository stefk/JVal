<?php

namespace JsonSchema;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider caseProvider
     *
     * @param string    $file
     * @param string    $title
     * @param mixed     $instance
     * @param \stdClass $schema
     * @param array     $expectedErrors
     */
    public function test($file, $title, $instance, \stdClass $schema, array $expectedErrors)
    {
        $validator = new Validator();
        $actualErrors = $validator->validate($instance, $schema);

        $rule = '**********************************************************************';
        $failureMessage = "%s\n\nCase: %s\n\nTest: %s\n\nSchema: %s\n\nInstance: %s\n\nExpected: %s\nActual: %s\n%s";
        $schema = trim(print_r($schema, true));
        $instance = trim(print_r($instance, true));
        $expected = count($expectedErrors) > 0 ? print_r($expectedErrors, true) : "none\n";
        $actual = count($actualErrors) > 0 ? print_r($actualErrors, true) : "none\n";

        $this->assertEqualErrors(
            $expectedErrors,
            $actualErrors,
            sprintf(
                $failureMessage,
                $rule,
                $file,
                $title,
                $schema,
                $instance,
                $expected,
                $actual,
                $rule
            )
        );
    }

    public function caseProvider()
    {
        $cases = [];

        foreach (new \DirectoryIterator(__DIR__.'/cases') as $item) {
            if ($item->isFile()) {
                $case = json_decode(file_get_contents($item->getPathname()));

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception(sprintf(
                        'json_encode error in file %s -> Error: %s',
                        $item->getFileName(),
                        json_last_error_msg()
                    ));
                }

                foreach ($case->tests as $test) {
                    if (!isset($test->valid) && !isset($test->invalid)) {
                        throw new \Exception(sprintf(
                            'Test case "%s %s" has neither "valid" or "invalid" data (file: %s)',
                            $case->title,
                            $test->title,
                            $item->getFilename()
                        ));
                    }

                    if (isset($test->valid)) {
                        foreach ($test->valid as $i => $instance) {
                            $cases[] = [
                                $item->getFilename(),
                                "{$case->title} {$test->title}, valid instance #{$i}",
                                $instance,
                                $test->schema,
                                []
                            ];
                        }
                    }

                    if (isset($test->invalid)) {
                        foreach ($test->invalid as $i => $set) {
                            $cases[] = [
                                $item->getFilename(),
                                "{$case->title} {$test->title}, invalid instance #{$i}",
                                $set->instance,
                                $test->schema,
                                array_map(function ($violation) {
                                    return (array)$violation;
                                }, $set->violations)
                            ];
                        }
                    }
                }
            }
        }

        return $cases;
    }

    private function assertEqualErrors(array $actual, array $expected, $message)
    {
        if (count($actual) !== count($expected)) {
            $this->assertTrue(false, $message);
        }

        foreach ($expected as $error) {
            if (!in_array($error, $actual)) {
                $this->assertTrue(false, $message);
            }
        }
    }
}
