<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal\Constraint;

use JVal\Constraint;
use JVal\Context;
use JVal\Testing\ConstraintTestCase;

class OneOfConstraintTest extends ConstraintTestCase
{
    public function testNormalizeIfOneOfIsNotArray()
    {
        $this->expectConstraintException('InvalidTypeException', '/oneOf');
        $schema = $this->loadSchema('invalid/oneOf-not-array');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfOneOfIsEmpty()
    {
        $this->expectConstraintException('EmptyArrayException', '/oneOf');
        $schema = $this->loadSchema('invalid/oneOf-empty');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfOneOfElementIsNotObject()
    {
        $this->expectConstraintException('InvalidTypeException', '/oneOf/1');
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
