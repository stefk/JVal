<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal\Constraint;

use JVal\Constraint;
use JVal\Context;
use JVal\Testing\ConstraintTestCase;

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

    public function testNormalizeThrowsIfPropertiesIsNotAnObject()
    {
        $this->expectConstraintException('InvalidTypeException', '/properties');
        $schema = $this->loadSchema('invalid/properties-not-object');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfPropertiesPropertyValueIsNotAnObject()
    {
        $this->expectConstraintException('InvalidTypeException', '/properties/foo');
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

    public function testNormalizeThrowsIfAdditionalPropertiesIsNotBooleanOrObject()
    {
        $this->expectConstraintException('InvalidTypeException', '/additionalProperties');
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

    public function testNormalizeThrowsIfPatternPropertiesIsNotAnObject()
    {
        $this->expectConstraintException('InvalidTypeException', '/patternProperties');
        $schema = $this->loadSchema('invalid/patternProperties-not-object');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfPatternPropertiesPropertyNameIsNotAValidRegex()
    {
        $this->expectConstraintException('InvalidRegexException', '/patternProperties//#this**not---a][valid[regex');
        $schema = $this->loadSchema('invalid/patternProperties-invalid-regex');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfPatternPropertiesPropertyValueIsNotAnObject()
    {
        $this->expectConstraintException('InvalidTypeException', '/patternProperties/regex');
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

    public function testDelimitersInPatternPropertiesAreEscapedBeforeTestingRegex()
    {
        $schema = $this->loadSchema('valid/patternProperties-with-slash');
        $context = new Context();
        $this->getConstraint()->normalize($schema, $context, $this->mockWalker());
        $this->assertEquals(0, $context->countViolations());
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
