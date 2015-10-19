<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Testing\ConstraintTestCase;

class MaximumConstraintTest extends ConstraintTestCase
{
    /**
     * @expectedException \JsonSchema\Exception\Constraint\MaximumNotPresentException
     */
    public function testNormalizeThrowsIfMaxNotPresent()
    {
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

    /**
     * @expectedException \JsonSchema\Exception\Constraint\MaximumNotNumberException
     */
    public function testNormalizeThrowsIfMaximumIsNotANumber()
    {
        $schema = $this->loadSchema('invalid/maximum-not-number');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    /**
     * @expectedException \JsonSchema\Exception\Constraint\ExclusiveMaximumNotBooleanException
     */
    public function testNormalizeThrowsIfExclusiveMaximumIsNotABoolean()
    {
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
