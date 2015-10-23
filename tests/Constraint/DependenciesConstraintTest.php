<?php

namespace JsonSchema\Constraint;

use JsonSchema\Context;
use JsonSchema\Testing\ConstraintTestCase;

class DependenciesConstraintTest extends ConstraintTestCase
{
    public function testNormalizeThrowsIfDependenciesIsNotObject()
    {
        $this->expectConstraintException('InvalidTypeException', '/dependencies');
        $schema = $this->loadSchema('invalid/dependencies-not-object');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfDependenciesPropertyIsNotObjectOrArray()
    {
        $this->expectConstraintException('InvalidTypeException', '/dependencies/bar');
        $schema = $this->loadSchema('invalid/dependencies-property-not-object-or-array');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeEnsuresSchemaDependenciesAreValid()
    {
        $schema = $this->loadSchema('valid/dependencies-schema-dependencies');
        $walker = $this->mockWalker();
        $walker->expects($this->at(0))
            ->method('parseSchema')
            ->with($schema->dependencies->foo);
        $walker->expects($this->at(1))
            ->method('parseSchema')
            ->with($schema->dependencies->bar);
        $this->getConstraint()->normalize($schema, new Context(), $walker);
    }

    public function testNormalizeThrowsIfPropertyDependenciesIsEmpty()
    {
        $this->expectConstraintException('EmptyArrayException', '/dependencies/bar');
        $schema = $this->loadSchema('invalid/dependencies-property-dependencies-empty');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfPropertyDependencyIsNotString()
    {
        $this->expectConstraintException('InvalidTypeException', '/dependencies/bar/1');
        $schema = $this->loadSchema('invalid/dependencies-property-not-string');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfPropertyDependencyIsNotUnique()
    {
        $this->expectConstraintException('NotUniqueException', '/dependencies/bar');
        $schema = $this->loadSchema('invalid/dependencies-property-dependencies-not-unique');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    protected function getConstraint()
    {
        return new DependenciesConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['dependencies'];
    }
}
