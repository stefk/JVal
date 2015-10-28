<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal\Constraint;

use JVal\Context;
use JVal\Testing\ConstraintTestCase;

class MaxItemsConstraintTest extends ConstraintTestCase
{
    public function testNormalizeThrowsIfMaxItemsIsNotAnInteger()
    {
        $this->expectConstraintException('InvalidTypeException', '/maxItems');
        $schema = $this->loadSchema('invalid/maxItems-not-integer');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfMaxItemsIsNotPositive()
    {
        $this->expectConstraintException('LessThanZeroException', '/maxItems');
        $schema = $this->loadSchema('invalid/maxItems-not-positive');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    protected function getConstraint()
    {
        return new MaxItemsConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['maxItems'];
    }
}
