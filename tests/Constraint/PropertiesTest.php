<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Testing\ConstraintTestCase;

class PropertiesTest extends ConstraintTestCase
{
    /**
     * @dataProvider absentKeywordProvider
     * @param $schemaName
     */
    public function testNormalizeSetsAbsentKeywordsToEmptySchema($schemaName)
    {
        $schema = $this->loadSchema($schemaName);
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
        $this->assertObjectHasAttribute('properties', $schema);
        $this->assertObjectHasAttribute('additionalProperties', $schema);
        $this->assertObjectHasAttribute('patternProperties', $schema);
        $this->assertEquals(new \stdClass(), $schema->properties);
        $this->assertEquals(new \stdClass(), $schema->additionalProperties);
        $this->assertEquals(new \stdClass(), $schema->patternProperties);
    }

    public function testNormalizeSetsAdditionalPropertiesToEmptySchemaIfSetToTrue()
    {
        $schema = $this->loadSchema('valid/additionalProperties-set-to-true');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
        $this->assertEquals(new \stdClass(), $schema->additionalProperties);
    }

    /**
     * @expectedException \JsonSchema\Exception\Constraint\PropertiesNotObjectException
     */
    public function testNormalizeThrowsIfPropertiesIsNotAnObject()
    {
        $schema = $this->loadSchema('invalid/properties-not-object');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    /**
     * @expectedException \JsonSchema\Exception\Constraint\PropertyValueNotObjectException
     */
    public function testNormalizeThrowsIfPropertiesPropertyValueIsNotAnObject()
    {
        $schema = $this->loadSchema('invalid/properties-property-value-not-object');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeEnsuresPropertiesPropertyIsAValidSchema()
    {
        $schema = $this->loadSchema('valid/properties-not-empty');
        $walker = $this->mockWalker();
        $walker->expects($this->at(0))
            ->method('parseSchema')
            ->with($schema->properties->foo);
        $this->getConstraint()->normalize($schema, new Context(), $walker);
    }

    /**
     * @expectedException \JsonSchema\Exception\Constraint\AdditionalPropertiesInvalidTypeException
     */
    public function testNormalizeThrowsIfAdditionalPropertiesIsNotBooleanOrObject()
    {
        $schema = $this->loadSchema('invalid/additionalProperties-not-object-or-boolean');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeEnsuresAdditionalPropertiesAsObjectIsAValidSchema()
    {
        $schema = $this->loadSchema('valid/additionalProperties-as-object');
        $walker = $this->mockWalker();
        $walker->expects($this->at(0))
            ->method('parseSchema')
            ->with($schema->additionalProperties);
        $this->getConstraint()->normalize($schema, new Context(), $walker);
    }

    /**
     * @expectedException \JsonSchema\Exception\Constraint\PatternPropertiesNotObjectException
     */
    public function testNormalizeThrowsIfPatternPropertiesIsNotAnObject()
    {
        $schema = $this->loadSchema('invalid/patternProperties-not-object');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    /**
     * @expectedException \JsonSchema\Exception\Constraint\PatternPropertiesInvalidRegexException
     */
    public function testNormalizeThrowsIfPatternPropertiesPropertyNameIsNotAValidRegex()
    {
        $schema = $this->loadSchema('invalid/patternProperties-invalid-regex');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    /**
     * @expectedException \JsonSchema\Exception\Constraint\PatternPropertyNotObjectException
     */
    public function testNormalizeThrowsIfPatternPropertiesPropertyValueIsNotAnObject()
    {
        $schema = $this->loadSchema('invalid/patternProperties-property-value-not-object');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeEnsuresPatternPropertiesPropertyValueIsAValidSchema()
    {
        $schema = $this->loadSchema('valid/patternProperties-not-empty');
        $walker = $this->mockWalker();
        $walker->expects($this->at(1))
            ->method('parseSchema')
            ->with($schema->patternProperties->regex);
        $this->getConstraint()->normalize($schema, new Context(), $walker);
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
            ['valid/properties-not-present'],
            ['valid/additionalProperties-not-present'],
            ['valid/patternProperties-not-present']
        ];
    }
}
