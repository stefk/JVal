<?php

namespace JsonSchema;

use JsonSchema\Testing\BaseTestCase;

class Draft4Test extends BaseTestCase
{
    /**
     * @dataProvider applyTestProvider
     *
     * @param string    $file
     * @param string    $title
     * @param mixed     $instance
     * @param \stdClass $schema
     * @param bool      $isInstanceValid
     */
    public function testApply(
        $file,
        $title,
        $instance,
        \stdClass $schema,
        $isInstanceValid
    )
    {
        $validator = Validator::buildDefault();
        $actualErrors = $validator->validate($instance, $schema);

        $this->assertValidationResult(
            $file,
            $title,
            $instance,
            $schema,
            $isInstanceValid,
            [],
            $actualErrors
        );
    }

    /**
     * Provider of #testApply().
     */
    public function applyTestProvider()
    {
        $testDir = realpath(__DIR__ . '/../../vendor/json-schema/test-suite/tests/draft4');
        $iterator = new \RecursiveDirectoryIterator($testDir);
        $tests = [];

        foreach (new \RecursiveIteratorIterator($iterator) as $item) {
            if ($item->isFile()) {
                $whiteList = $this->whiteListFiles();
                $blackList = $this->blackListFiles();

                if ($whiteList !== false && !in_array($item->getBaseName(), $whiteList)) {
                    continue;
                }

                if (in_array($item->getBaseName(), $blackList)) {
                    continue;
                }

                $cases = $this->loadJsonFromFile($item->getPathname());

                foreach ($cases as $case) {
                    foreach ($case->tests as $test) {
                        if (in_array($test->description, $this->blackListTests())) {
                            continue;
                        }

                        $tests[] = array(
                            $item->getFilename(),
                            "{$case->description} - {$test->description}",
                            $test->data,
                            $case->schema,
                            $test->valid
                        );
                    }
                }
            }
        }

        return $tests;
    }

    private function blackListFiles()
    {
        return [];
    }

    private function blackListTests()
    {
        // those two tests won't never pass in PHP
        return [
            'a bignum is an integer',
            'a negative bignum is an integer'
        ];
    }

    private function whiteListFiles()
    {
        return ['ref.json'];
    }
}
