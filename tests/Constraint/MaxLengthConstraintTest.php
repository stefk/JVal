<?php

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
