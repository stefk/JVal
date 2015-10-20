<?php

namespace JsonSchema\Constraint;

use JsonSchema\Context;
use JsonSchema\Testing\ConstraintTestCase;

class MinLengthConstraintTest extends ConstraintTestCase
{
    public function testNormalizeThrowsIfMinLengthIsNotAnInteger()
    {
        $this->expectConstraintException('InvalidTypeException', '/minLength');
        $schema = $this->loadSchema('invalid/minLength-not-integer');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfMinLengthIsNotPositive()
    {
        $this->expectConstraintException('LessThanZeroException', '/minLength');
        $schema = $this->loadSchema('invalid/minLength-not-positive');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    protected function getConstraint()
    {
        return new MinLengthConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['minLength'];
    }
}
