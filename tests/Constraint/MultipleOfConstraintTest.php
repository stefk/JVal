<?php

namespace JsonSchema\Constraint;

use JsonSchema\Exception\ConstraintException;
use JsonSchema\Testing\ConstraintTestCase;

class MultipleOfConstraintTest extends ConstraintTestCase
{
    public function testNormalizeThrowsIfNotANumber()
    {
        $this->expectException(ConstraintException::MULTIPLE_OF_NOT_A_NUMBER);
        $constraint = new MultipleOfConstraint();
        $schema = $this->loadSchema('multiple-of-not-a-number');
        $constraint->normalize($schema);
    }

    /**
     * @dataProvider invalidSchemaProvider
     * @param string $schemaName
     */
    public function testNormalizeThrowsOnNonPositiveNumber($schemaName)
    {
        $this->expectException(ConstraintException::MULTIPLE_OF_NOT_POSITIVE);
        $constraint = new MultipleOfConstraint();
        $schema = $this->loadSchema($schemaName);
        $constraint->normalize($schema);
    }

    public function invalidSchemaProvider()
    {
        return [
            ['multiple-of-not-positive-1'],
            ['multiple-of-not-positive-2']
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
