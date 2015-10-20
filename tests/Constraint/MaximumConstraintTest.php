<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Testing\ConstraintTestCase;

class MaximumConstraintTest extends ConstraintTestCase
{
    public function testNormalizeThrowsIfMaxNotPresent()
    {
        $this->expectConstraintException('MissingKeywordException', '', 'maximum');
        $schema = $this->loadSchema('invalid/maximum-not-present');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeSetsExclusiveMaxToFalseIfNotPresent()
    {
        $schema = $this->loadSchema('valid/exclusiveMaximum-not-present');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
        $this->assertObjectHasAttribute('exclusiveMaximum', $schema);
        $this->assertEquals(false, $schema->exclusiveMaximum);
    }

    public function testNormalizeThrowsIfMaximumIsNotANumber()
    {
        $this->expectConstraintException('InvalidTypeException', '/maximum');
        $schema = $this->loadSchema('invalid/maximum-not-number');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfExclusiveMaximumIsNotABoolean()
    {
        $this->expectConstraintException('InvalidTypeException', '/exclusiveMaximum');
        $schema = $this->loadSchema('invalid/exclusiveMaximum-not-boolean');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    protected function getConstraint()
    {
        return new MaximumConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['maximum'];
    }
}
