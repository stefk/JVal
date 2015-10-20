<?php

namespace JsonSchema\Constraint;

use JsonSchema\Context;
use JsonSchema\Testing\ConstraintTestCase;

class UniqueItemsConstraintTest extends ConstraintTestCase
{
    public function testNormalizeThrowsIfMaxLengthIsNotAnInteger()
    {
        $this->expectConstraintException('InvalidTypeException', '/uniqueItems');
        $schema = $this->loadSchema('invalid/uniqueItems-not-boolean');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    protected function getConstraint()
    {
        return new UniqueItemsConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['uniqueItems'];
    }
}
