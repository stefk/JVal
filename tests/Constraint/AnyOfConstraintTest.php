<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Testing\ConstraintTestCase;

class AnyOfConstraintTest extends ConstraintTestCase
{
    /**
     * @expectedException \JsonSchema\Exception\Constraint\AnyOfNotArrayException
     */
    public function testNormalizeIfAnyOfIsNotArray()
    {
        $schema = $this->loadSchema('invalid/anyOf-not-array');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    /**
     * @expectedException \JsonSchema\Exception\Constraint\AnyOfEmptyException
     */
    public function testNormalizeThrowsIfAnyOfIsEmpty()
    {
        $schema = $this->loadSchema('invalid/anyOf-empty');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    /**
     * @expectedException \JsonSchema\Exception\Constraint\AnyOfElementNotObjectException
     */
    public function testNormalizeThrowsIfAnyOfElementIsNotObject()
    {
        $schema = $this->loadSchema('invalid/anyOf-element-not-object');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeEnsureEverySubSchemaIsValid()
    {
        $schema = $this->loadSchema('valid/anyOf-valid-sub-schemas');
        $walker = $this->mockWalker();
        $walker->expects($this->exactly(2))
            ->method('parseSchema')
            ->withConsecutive($schema->anyOf[0], $schema->anyOf[1]);
        $this->getConstraint()->normalize($schema, new Context(), $walker);
    }

    protected function getConstraint()
    {
        return new AnyOfConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['anyOf'];
    }
}
