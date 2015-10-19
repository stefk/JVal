<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Testing\ConstraintTestCase;

class MaxItemsConstraintTest extends ConstraintTestCase
{
    /**
     * @expectedException \JsonSchema\Exception\Constraint\MaxItemsNotIntegerException
     */
    public function testNormalizeThrowsIfMaxItemsIsNotAnInteger()
    {
        $schema = $this->loadSchema('invalid/maxItems-not-integer');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    /**
     * @dataProvider nonPositiveMaxItemsProvider
     * @expectedException \JsonSchema\Exception\Constraint\MaxItemsNotPositiveException
     *
     * @param $schemaName
     */
    public function testNormalizeThrowsIfMaxItemsIsNotPositive($schemaName)
    {
        $schema = $this->loadSchema($schemaName);
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function nonPositiveMaxItemsProvider()
    {
        return [
            ['invalid/maxItems-not-positive-1'],
            ['invalid/maxItems-not-positive-2']
        ];
    }

    protected function getConstraint()
    {
        return new MaxItemsConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['maxItems'];
    }
}
