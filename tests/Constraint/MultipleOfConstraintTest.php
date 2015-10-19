<?php

namespace JsonSchema\Constraint;

use JsonSchema\Context;
use JsonSchema\Testing\ConstraintTestCase;

class MultipleOfConstraintTest extends ConstraintTestCase
{
    /**
     * @expectedException \JsonSchema\Exception\Constraint\MultipleOfNotNumberException
     */
    public function testNormalizeThrowsIfNotANumber()
    {
        $schema = $this->loadSchema('invalid/multiple-of-not-number');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    /**
     * @dataProvider nonPositiveMultipleOfProvider
     * @expectedException \JsonSchema\Exception\Constraint\MultipleOfNotPositiveException
     *
     * @param string $schemaName
     */
    public function testNormalizeThrowsOnNonPositiveNumber($schemaName)
    {
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
