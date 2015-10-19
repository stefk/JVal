<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Testing\ConstraintTestCase;

class AllOfConstraintTest extends ConstraintTestCase
{
    /**
     * @expectedException \JsonSchema\Exception\Constraint\AllOfNotArrayException
     */
    public function testNormalizeIfAllOfIsNotArray()
    {
        $schema = $this->loadSchema('invalid/allOf-not-array');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    /**
     * @expectedException \JsonSchema\Exception\Constraint\AllOfEmptyException
     */
    public function testNormalizeThrowsIfAllOfIsEmpty()
    {
        $schema = $this->loadSchema('invalid/allOf-empty');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    /**
     * @expectedException \JsonSchema\Exception\Constraint\AllOfElementNotObjectException
     */
    public function testNormalizeThrowsIfAllOfElementIsNotObject()
    {
        $schema = $this->loadSchema('invalid/allOf-element-not-object');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeEnsureEverySubSchemaIsValid()
    {
        $schema = $this->loadSchema('valid/allOf-valid-sub-schemas');
        $walker = $this->mockWalker();
        $walker->expects($this->exactly(2))
            ->method('parseSchema')
            ->withConsecutive($schema->allOf[0], $schema->allOf[1]);
        $this->getConstraint()->normalize($schema, new Context(), $walker);
    }

    protected function getConstraint()
    {
        return new AllOfConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['allOf'];
    }
}
