<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal\Testing;

/**
 * Test case for testing against actual JSON data stored in dedicated files.
 * Format examples can be found in the tests/Data/constraints directory.
 */
abstract class DataTestCase extends BaseTestCase
{
    /**
     * @codeCoverageIgnore (data provider is executed before test is launched)
     *
     * Data provider collecting test cases from JSON files.
     */
    public function fileDataProvider()
    {
        $caseDir = realpath($this->getDataDirectory());
        $caseNames = $this->getCaseFileNames();
        $caseExt = $caseNames ? '.json' : '';
        $caseNames = $caseNames ?: array_diff(scandir($caseDir), ['..', '.']);
        $tests = [];

        foreach ($caseNames as $caseName) {
            $caseFile = "{$caseDir}/{$caseName}{$caseExt}";
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
                            clone $test->schema,
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
     * Returns the path the test data directory.
     *
     * @return string
     */
    abstract protected function getDataDirectory();

    /**
     * Returns the names of the test cases to be loaded. If no names are
     * returned, all the cases found in the data directory will be loaded.
     *
     * @return mixed
     */
    abstract protected function getCaseFileNames();
}
