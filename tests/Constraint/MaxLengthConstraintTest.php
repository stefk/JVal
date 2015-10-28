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

class MaxLengthConstraintTest extends ConstraintTestCase
{
    public function testNormalizeThrowsIfMaxLengthIsNotAnInteger()
    {
        $this->expectConstraintException('InvalidTypeException', '/maxLength');
        $schema = $this->loadSchema('invalid/maxLength-not-integer');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfMaxLengthIsNotPositive()
    {
        $this->expectConstraintException('LessThanZeroException', '/maxLength');
        $schema = $this->loadSchema('invalid/maxLength-not-positive');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    protected function getConstraint()
    {
        return new MaxLengthConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['maxLength'];
    }
}
