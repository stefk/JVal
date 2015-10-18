<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Testing\ConstraintTestCase;

class PropertiesTest extends ConstraintTestCase
{
    /**
     * @dataProvider absentKeywordProvider
     * @param $schemaName
     */
    public function testNormalizeSetsAbsentKeywordsToEmptySchema($schemaName)
    {
        $this->markTestSkipped();
    }

    public function testNormalizeSetsAdditionalPropertiesToEmptySchemaIfSetToTrue()
    {
        $this->markTestSkipped();
    }

    public function testNormalizeThrowsIfPropertiesIsNotAnObject()
    {
        $this->markTestSkipped();
    }

    public function testNormalizeThrowsIfPropertiesPropertyValueIsNotAnObject()
    {
        $this->markTestSkipped();
    }

    public function testNormalizeEnsuresPropertiesPropertyIsAValidSchema()
    {
        $this->markTestSkipped();
    }

    public function testNormalizeThrowsIfAdditionalPropertiesIsNotBooleanOrObject()
    {
        $this->markTestSkipped();
    }

    public function testNormalizeEnsuresAdditionalPropertiesAsObjectIsAValidSchema()
    {
        $this->markTestSkipped();
    }

    public function testNormalizeThrowsIfPatternPropertiesIsNotAnObject()
    {
        $this->markTestSkipped();
    }

    public function testNormalizeThrowsIfPatternPropertiesPropertyNameIsNotAValidRegex()
    {
        $this->markTestSkipped();
    }

    public function testNormalizeThrowsIfPatternPropertiesPropertyValueIsNotAnObject()
    {
        $this->markTestSkipped();
    }

    public function testNormalizeEnsuresPatternPropertiesPropertyValueIsAValidSchema()
    {
        $this->markTestSkipped();
    }

    protected function getConstraint()
    {
        return new PropertiesConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['properties'];
    }

    public function absentKeywordProvider()
    {
        return [
            'valid/properties-not-present',
            'valid/additionalProperties-not-present',
            'valid/patternProperties-not-present'
        ];
    }
}
