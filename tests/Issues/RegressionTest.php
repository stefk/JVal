<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal;

use JVal\Testing\DataTestCase;

class RegressionTest extends DataTestCase
{
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
        $validator = Validator::buildDefault();
        $actualErrors = $validator->validate($instance, $schema, $this->getLocalUri($file));

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
     * {@inheritDoc}
     */
    protected function getDataDirectory()
    {
        return __DIR__.'/../Data/issues';
    }

    /**
     * {@inheritDoc}
     */
    protected function getCaseFileNames()
    {
        return false;
    }
}
