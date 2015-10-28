<?php

namespace JVal\Constraint;

use JVal\Constraint;
use JVal\Context;
use JVal\Testing\ConstraintTestCase;

class ItemsConstraintTest extends ConstraintTestCase
{
    public function testNormalizeSetsItemsToEmptySchemaIfNotPresent()
    {
        $schema = $this->loadSchema('valid/items-not-present');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
        $this->assertObjectHasAttribute('items', $schema);
        $this->assertEquals(new \stdClass(), $schema->items);
    }

    public function testNormalizeSetsAdditionalItemsToEmptySchemaIfNotPresent()
    {
        $schema = $this->loadSchema('valid/additionalItems-not-present');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
        $this->assertObjectHasAttribute('additionalItems', $schema);
        $this->assertEquals(new \stdClass(), $schema->additionalItems);
    }

    public function testNormalizeSetsAdditionalItemsToEmptySchemaIfSetToTrue()
    {
        $schema = $this->loadSchema('valid/additionalItems-set-to-true');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
        $this->assertEquals(new \stdClass(), $schema->additionalItems);
    }

    public function testNormalizeThrowsIfItemsIsNotObjectOrArray()
    {
        $this->expectConstraintException('InvalidTypeException', '/items');
        $schema = $this->loadSchema('invalid/items-not-object-or-array');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeEnsuresItemsObjectIsAValidSchema()
    {
        $schema = $this->loadSchema('valid/items-object');
        $walker = $this->mockWalker();
        $walker->expects($this->at(0))
            ->method('parseSchema')
            ->with($schema->items);
        $this->getConstraint()->normalize($schema, new Context(), $walker);
    }

    public function testNormalizeThrowsIfItemsElementIsNotObject()
    {
        $this->expectConstraintException('InvalidTypeException', '/items/1');
        $schema = $this->loadSchema('invalid/items-element-not-object');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeEnsuresItemsElementIsAValidSchema()
    {
        $schema = $this->loadSchema('valid/items-array');
        $walker = $this->mockWalker();
        $walker->expects($this->at(1))
            ->method('parseSchema')
            ->with($schema->items[0]);
        $walker->expects($this->at(2))
            ->method('parseSchema')
            ->with($schema->items[1]);
        $this->getConstraint()->normalize($schema, new Context(), $walker);
    }

    public function testNormalizeThrowsIfAdditionalItemsIsNotBooleanOrObject()
    {
        $this->expectConstraintException('InvalidTypeException', '/additionalItems');
        $schema = $this->loadSchema('invalid/additionalItems-not-object-or-boolean');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeEnsuresAdditionalItemsAsObjectIsAValidSchema()
    {
        $schema = $this->loadSchema('valid/additionalItems-object');
        $walker = $this->mockWalker();
        $walker->expects($this->at(1))
            ->method('parseSchema')
            ->with($schema->additionalItems);
        $this->getConstraint()->normalize($schema, new Context(), $walker);
    }

    protected function getConstraint()
    {
        return new ItemsConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['items'];
    }
}
