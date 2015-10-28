<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \LogicException
     */
    public function testLeaveNodeThrowsOnEmptyStack()
    {
        $context = new Context();
        $context->leaveNode();
    }
}
