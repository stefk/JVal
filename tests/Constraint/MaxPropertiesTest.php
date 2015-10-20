<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Testing\ConstraintTestCase;

class MaxPropertiesConstraintTest extends ConstraintTestCase
{
    public function testNormalizeThrowsIfMaxPropertiesIsNotAnInteger()
    {
        $this->expectConstraintException('InvalidTypeException', '/maxProperties');
        $schema = $this->loadSchema('invalid/maxProperties-not-integer');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    /**
     * @dataProvider nonPositiveMaxPropertiesProvider
     *
     * @param $schemaName
     */
    public function testNormalizeThrowsIfMaxPropertiesIsNotPositive($schemaName)
    {
        $this->expectConstraintException('LessThanZeroException', '/maxProperties');
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
