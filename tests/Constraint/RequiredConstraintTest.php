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

class RequiredConstraintTest extends ConstraintTestCase
{
    public function testNormalizeThrowsIfRequiredIsNotArray()
    {
        $this->expectConstraintException('InvalidTypeException', '/required');
        $schema = $this->loadSchema('invalid/required-not-array');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfRequiredIsEmpty()
    {
        $this->expectConstraintException('EmptyArrayException', '/required');
        $schema = $this->loadSchema('invalid/required-empty');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfRequiredPropertyIsNotString()
    {
        $this->expectConstraintException('InvalidTypeException', '/required/1');
        $schema = $this->loadSchema('invalid/required-property-not-string');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfRequiredPropertyIsNotUnique()
    {
        $this->expectConstraintException('NotUniqueException', '/required');
        $schema = $this->loadSchema('invalid/required-property-not-unique');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    protected function getConstraint()
    {
        return new RequiredConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['required'];
    }
}
