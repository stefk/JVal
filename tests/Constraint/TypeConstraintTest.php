<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Testing\ConstraintTestCase;

class TypeConstraintTest extends ConstraintTestCase
{
    /**
     * @expectedException \JsonSchema\Exception\Constraint\TypeInvalidTypeException
     */
    public function testNormalizeThrowsIfTypeIsNotStringOrArray()
    {
        $schema = $this->loadSchema('invalid/type-not-string-or-array');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    /**
     * @expectedException \JsonSchema\Exception\Constraint\TypeElementNotStringException
     */
    public function testNormalizeThrowsIfTypeArrayElementIsNotAString()
    {
        $schema = $this->loadSchema('invalid/type-element-not-string');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    /**
     * @expectedException \JsonSchema\Exception\Constraint\TypeElementNotUniqueException
     */
    public function testNormalizeThrowsIfTypeArrayElementIsNotUnique()
    {

        $schema = $this->loadSchema('invalid/type-element-not-unique');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    /**
     * @expectedException \JsonSchema\Exception\Constraint\TypeNotPrimitiveTypeException
     */
    public function testNormalizeThrowsIfTypeIsNotADefinedPrimitiveType()
    {

        $schema = $this->loadSchema('invalid/type-not-primitive');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    /**
     * @expectedException \JsonSchema\Exception\Constraint\TypeNotPrimitiveTypeException
     */
    public function testNormalizeThrowsIfTypeElementIsNotADefinedPrimitiveType()
    {
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
