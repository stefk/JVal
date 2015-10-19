<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Testing\ConstraintTestCase;

class OneOfConstraintTest extends ConstraintTestCase
{
    /**
     * @expectedException \JsonSchema\Exception\Constraint\OneOfNotArrayException
     */
    public function testNormalizeIfOneOfIsNotArray()
    {
        $schema = $this->loadSchema('invalid/oneOf-not-array');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    /**
     * @expectedException \JsonSchema\Exception\Constraint\OneOfEmptyException
     */
    public function testNormalizeThrowsIfOneOfIsEmpty()
    {
        $schema = $this->loadSchema('invalid/oneOf-empty');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    /**
     * @expectedException \JsonSchema\Exception\Constraint\OneOfElementNotObjectException
     */
    public function testNormalizeThrowsIfOneOfElementIsNotObject()
    {
        $schema = $this->loadSchema('invalid/oneOf-element-not-object');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeEnsureEverySubSchemaIsValid()
    {
        $schema = $this->loadSchema('valid/oneOf-valid-sub-schemas');
        $walker = $this->mockWalker();
        $walker->expects($this->exactly(2))
            ->method('parseSchema')
            ->withConsecutive($schema->oneOf[0], $schema->oneOf[1]);
        $this->getConstraint()->normalize($schema, new Context(), $walker);
    }

    protected function getConstraint()
    {
        return new OneOfConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['oneOf'];
    }
}
