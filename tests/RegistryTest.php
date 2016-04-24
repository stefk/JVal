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
    public function testGetConstraintsThrowOnUnsupportedVersion()
    {
        $registry = new Registry();
        $registry->getConstraints('unknown');
    }

    public function testHasKeyword()
    {
        $registry = new Registry();
        $this->assertFalse($registry->hasKeyword(Registry::VERSION_CURRENT, 'unknown'));
        $this->assertTrue($registry->hasKeyword(Registry::VERSION_CURRENT, 'maximum'));
    }
}
