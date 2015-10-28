<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal;

class RegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \JVal\Exception\UnsupportedVersionException
     */
    public function testLoadConstraintsThrowOnUnsupportedVersion()
    {
        $registry = new Registry();
        $registry->loadConstraintsFor('unknown');
    }

    /**
     * @expectedException \LogicException
     */
    public function testLoadConstraintsThrowIfNoConstraintsLoaded()
    {
        $registry = new Registry();
        $registry->getConstraints();
    }
}
