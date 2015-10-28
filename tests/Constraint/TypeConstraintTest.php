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
        $this->expectConstraintException('InvalidTypeException', '/type/1');
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
        $this->expectConstraintException('NotPrimitiveTypeException', '/type/1');
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
