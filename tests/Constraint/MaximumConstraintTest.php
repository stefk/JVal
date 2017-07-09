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

class MaximumConstraintTest extends ConstraintTestCase
{

    public function testNormalizeThrowsIfMaximumIsNotANumber()
    {
        $this->expectConstraintException('InvalidTypeException', '/maximum');
        $schema = $this->loadSchema('invalid/maximum-not-number');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfExclusiveMaximumIsNotANumber()
    {
        $this->expectConstraintException('InvalidTypeException', '/exclusiveMaximum');
        $schema = $this->loadSchema('invalid/exclusiveMaximum-not-number');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    protected function getConstraint()
    {
        return new MaximumConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['maximum'];
    }
}
