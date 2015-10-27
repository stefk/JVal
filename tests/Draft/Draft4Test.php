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
        $remoteDir = realpath(__DIR__ . '/../../vendor/json-schema/test-suite/remotes');
        $validator = Validator::buildDefault(function ($uri) use ($remoteDir) {
            return str_replace('http://localhost:1234', 'file://' . $remoteDir, $uri);
        });
        $actualErrors = $validator->validate($instance, $schema, 'file://' . $file);

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

                $caseCount = count($this->loadJsonFromFile($item->getPathname()));

                for ($i = 0; $i < $caseCount; ++$i) {
                    // As validation begins with a normalization step, we cannot
                    // share the same schema instance between tests, so we reload
                    // it for each case.
                    $cases = $this->loadJsonFromFile($item->getPathname());

                    foreach ($cases[$i]->tests as $test) {
                        if (in_array($test->description, $this->blackListTests())) {
                            continue;
                        }

                        $tests[] = array(
                            $item->getPathName(),
                            "{$cases[$i]->description} - {$test->description}",
                            $test->data,
                            $cases[$i]->schema,
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
        // those two tests won't never pass in PHP (bignums encountered in
        // JSON strings are automatically converted to float)
        return [
            'a bignum is an integer',
            'a negative bignum is an integer'
        ];
    }

    private function whiteListFiles()
    {
        return false;
    }
}
