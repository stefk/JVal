<?php

namespace JsonSchema\Constraint;

use JsonSchema\Context;
use JsonSchema\Testing\ConstraintTestCase;

class NotConstraintTest extends ConstraintTestCase
{
    public function testNormalizeThrowsIfNotIsNotObject()
    {
        $this->expectConstraintException('InvalidTypeException', '/not');
        $schema = $this->loadSchema('invalid/not-not-object');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeEnsuresNotIsAValidSchema()
    {
        $schema = $this->loadSchema('valid/not-schema');
        $walker = $this->mockWalker();
        $walker->expects($this->at(0))
            ->method('parseSchema')
            ->with($schema->not);
        $this->getConstraint()->normalize($schema, new Context(), $walker);
    }

    protected function getConstraint()
    {
        return new NotConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['not'];
    }
}
