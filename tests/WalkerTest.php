<?php

namespace JsonSchema;

use JsonSchema\Testing\BaseTestCase;

class WalkerTest extends BaseTestCase
{
    /**
     * @var Walker
     */
    private $walker;

    protected function setUp()
    {
        $registry = new Registry();
        $resolver = new Resolver();
        $this->walker = new Walker($registry, $resolver);
    }

    public function testParseSchemaWithReference()
    {
        $schema = $this->loadSchema('valid/cross-reference');
        $this->walker->parseSchema($schema, new Context());

        $this->assertObjectHasAttribute('properties', $schema);
        $this->assertObjectHasAttribute('additionalProperties', $schema);
        $this->assertObjectHasAttribute('patternProperties', $schema);

        $this->assertEquals(new \stdClass(), $schema->additionalProperties);
        $this->assertEquals(new \stdClass(), $schema->patternProperties);

        $this->assertObjectHasAttribute('foo', $schema->properties);
        $this->assertObjectHasAttribute('type', $schema->properties->foo);
        $this->assertEquals('string', $schema->properties->foo->type);

        $this->assertObjectHasAttribute('bar', $schema->properties);
        $this->assertSame($schema->properties->foo, $schema->properties->bar);
    }

    public function testParseSchemaWithRecursiveReference()
    {
        $schema = $this->loadSchema('valid/recursive-reference');
        $this->walker->parseSchema($schema, new Context());

        $this->assertObjectHasAttribute('properties', $schema);
        $this->assertObjectHasAttribute('additionalProperties', $schema);
        $this->assertObjectHasAttribute('patternProperties', $schema);

        $this->assertEquals(false, $schema->additionalProperties);
        $this->assertEquals(new \stdClass(), $schema->patternProperties);

        $this->assertObjectHasAttribute('foo', $schema->properties);
        $this->assertObjectNotHasAttribute('$ref', $schema->properties->foo);
        $this->assertSame($schema, $schema->properties->foo);
    }

    public function testApplyConstraintsWithRecursiveReference()
    {
        $schema = $this->loadSchema('valid/recursive-reference');
        $this->walker->parseSchema($schema, new Context());

        $instance = new \stdClass();
        $instance->foo = new \stdClass();
        $instance->foo->foo = false;

        $this->walker->applyConstraints($instance, $schema, new Context());
    }
}
