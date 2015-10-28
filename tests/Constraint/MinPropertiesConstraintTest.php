<?php

namespace JVal\Constraint;

use JVal\Constraint;
use JVal\Context;
use JVal\Testing\ConstraintTestCase;

class MinPropertiesConstraintTest extends ConstraintTestCase
{
    public function testNormalizeThrowsIfMinPropertiesIsNotAnInteger()
    {
        $this->expectConstraintException('InvalidTypeException', '/minProperties');
        $schema = $this->loadSchema('invalid/minProperties-not-integer');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfMinPropertiesIsNotPositive()
    {
        $this->expectConstraintException('LessThanZeroException', '/minProperties');
        $schema = $this->loadSchema('invalid/minProperties-not-positive');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    protected function getConstraint()
    {
        return new MinPropertiesConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['minProperties'];
    }
}
