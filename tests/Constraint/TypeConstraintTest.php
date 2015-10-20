<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Testing\ConstraintTestCase;

class TypeConstraintTest extends ConstraintTestCase
{
    public function testNormalizeThrowsIfTypeIsNotStringOrArray()
    {
        $this->expectConstraintException('InvalidTypeException', '/type');
        $schema = $this->loadSchema('invalid/type-not-string-or-array');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfTypeArrayElementIsNotAString()
    {
        $this->expectConstraintException('InvalidTypeException', '/type/2');
        $schema = $this->loadSchema('invalid/type-element-not-string');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfTypeArrayElementAreNotUnique()
    {
        $this->expectConstraintException('NotUniqueException', '/type');
        $schema = $this->loadSchema('invalid/type-element-not-unique');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfTypeIsNotADefinedPrimitiveType()
    {
        $this->expectConstraintException('NotPrimitiveTypeException', '/type');
        $schema = $this->loadSchema('invalid/type-not-primitive');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfTypeElementIsNotADefinedPrimitiveType()
    {
        $this->expectConstraintException('NotPrimitiveTypeException', '/type/2');
        $schema = $this->loadSchema('invalid/type-element-not-primitive');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    protected function getConstraint()
    {
        return new TypeConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['type'];
    }
}
