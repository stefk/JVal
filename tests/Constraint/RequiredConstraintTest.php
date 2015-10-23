<?php

namespace JsonSchema\Constraint;

use JsonSchema\Context;
use JsonSchema\Testing\ConstraintTestCase;

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
