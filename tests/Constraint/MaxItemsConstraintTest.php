<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\ConstraintException;
use JsonSchema\Testing\ConstraintTestCase;

class MaxItemsConstraintTest extends ConstraintTestCase
{
    public function testNormalizeThrowsIfMaxItemsIsNotAnInteger()
    {
        $this->expectException(ConstraintException::MAX_ITEMS_NOT_INTEGER);
        $schema = $this->loadSchema('invalid/maxItems-not-integer');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    /**
     * @dataProvider nonPositiveMaxItemsProvider
     * @param $schemaName
     */
    public function testNormalizeThrowsIfMaxItemsIsNotPositive($schemaName)
    {
        $this->expectException(ConstraintException::MAX_ITEMS_NOT_POSITIVE);
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
