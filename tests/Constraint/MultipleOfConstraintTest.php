<?php

namespace JsonSchema\Constraint;

use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;
use JsonSchema\Testing\ConstraintTestCase;

class MultipleOfConstraintTest extends ConstraintTestCase
{
    public function testNormalizeThrowsIfNotANumber()
    {
        $this->expectException(ConstraintException::MULTIPLE_OF_NOT_NUMBER);
        $schema = $this->loadSchema('invalid/multiple-of-not-number');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    /**
     * @dataProvider nonPositiveMultipleOfProvider
     * @param string $schemaName
     */
    public function testNormalizeThrowsOnNonPositiveNumber($schemaName)
    {
        $this->expectException(ConstraintException::MULTIPLE_OF_NOT_POSITIVE);
        $schema = $this->loadSchema($schemaName);
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function nonPositiveMultipleOfProvider()
    {
        return [
            ['invalid/multiple-of-not-positive-1'],
            ['invalid/multiple-of-not-positive-2']
        ];
    }

    protected function getConstraint()
    {
        return new MultipleOfConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['multipleOf'];
    }
}
