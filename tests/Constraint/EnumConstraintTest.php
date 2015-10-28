<?php

namespace JVal\Constraint;

use JVal\Context;
use JVal\Testing\ConstraintTestCase;

class EnumConstraintTest extends ConstraintTestCase
{
    public function testNormalizeThrowsIfEnumIsNotArray()
    {
        $this->expectConstraintException('InvalidTypeException', '/enum');
        $schema = $this->loadSchema('invalid/enum-not-array');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfEnumIsEmpty()
    {
        $this->expectConstraintException('EmptyArrayException', '/enum');
        $schema = $this->loadSchema('invalid/enum-empty');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfEnumElementsAreNotUnique()
    {
        $this->expectConstraintException('NotUniqueException', '/enum');
        $schema = $this->loadSchema('invalid/enum-elements-not-unique');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    protected function getConstraint()
    {
        return new EnumConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['enum'];
    }
}
