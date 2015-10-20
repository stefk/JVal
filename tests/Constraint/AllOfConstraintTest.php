<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Testing\ConstraintTestCase;

class AllOfConstraintTest extends ConstraintTestCase
{
    public function testNormalizeIfAllOfIsNotArray()
    {
        $this->expectConstraintException('InvalidTypeException', '/allOf');
        $schema = $this->loadSchema('invalid/allOf-not-array');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfAllOfIsEmpty()
    {
        $this->expectConstraintException('EmptyArrayException', '/allOf');
        $schema = $this->loadSchema('invalid/allOf-empty');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfAllOfElementIsNotObject()
    {
        $this->expectConstraintException('InvalidTypeException', '/allOf/2');
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
