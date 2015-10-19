<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Testing\ConstraintTestCase;

class MaxPropertiesConstraintTest extends ConstraintTestCase
{
    /**
     * @expectedException \JsonSchema\Exception\Constraint\MaxPropertiesNotIntegerException
     */
    public function testNormalizeThrowsIfMaxPropertiesIsNotAnInteger()
    {
        $schema = $this->loadSchema('invalid/maxProperties-not-integer');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    /**
     * @dataProvider nonPositiveMaxPropertiesProvider
     * @expectedException \JsonSchema\Exception\Constraint\MaxPropertiesNotPositiveException
     *
     * @param $schemaName
     */
    public function testNormalizeThrowsIfMaxPropertiesIsNotPositive($schemaName)
    {
        $schema = $this->loadSchema($schemaName);
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    protected function getConstraint()
    {
        return new MaxPropertiesConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['maxProperties'];
    }

    public function nonPositiveMaxPropertiesProvider()
    {
        return [
            ['invalid/maxProperties-not-positive-1'],
            ['invalid/maxProperties-not-positive-2']
        ];
    }
}
