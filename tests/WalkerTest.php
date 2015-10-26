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
        $resolver->setResolveHook(function ($uri) {
            return str_replace(
                'http://localhost:1234',
                'file://' . __DIR__ . '/Data/schemas',
                $uri
            );
        });
        $this->walker = new Walker($registry, $resolver);
    }

    public function testParseSchemaWithLocalReferences()
    {
        $schema = $this->loadSchema('valid/local-references');
        $parsed = $this->walker->parseSchema($schema, new Context());
        $this->assertObjectHasAttribute('properties', $parsed);

        $this->assertObjectHasAttribute('foo', $parsed->properties);
        $this->assertObjectNotHasAttribute('$ref', $parsed->properties->foo);
        $this->assertSame($schema->definitions->foo, $parsed->properties->foo);

        $this->assertObjectHasAttribute('bar', $parsed->properties);
        $this->assertObjectNotHasAttribute('$ref', $parsed->properties->bar);
        $this->assertSame($schema->definitions->bar, $parsed->properties->bar);
    }

    public function testParseSchemaWithRecursiveReferences()
    {
        $schema = $this->loadSchema('valid/recursive-references');
        $parsed = $this->walker->parseSchema($schema, new Context());

        $this->assertObjectHasAttribute('properties', $parsed);
        $this->assertObjectHasAttribute('additionalProperties', $parsed);
        $this->assertObjectHasAttribute('patternProperties', $parsed);

        $this->assertEquals(false, $parsed->additionalProperties);
        $this->assertEquals(new \stdClass(), $parsed->patternProperties);

        $this->assertObjectHasAttribute('foo', $parsed->properties);
        $this->assertObjectNotHasAttribute('$ref', $parsed->properties->foo);
        $this->assertSame($parsed, $parsed->properties->foo);

        $this->assertObjectHasAttribute('bar', $parsed->properties);
        $this->assertObjectNotHasAttribute('$ref', $parsed->properties->bar);
        $this->assertSame($parsed->properties->bar, $parsed->definitions->bar);
    }

    public function testParseSchemaWithReferenceOnly()
    {
        $schema = $this->loadSchema('valid/remote-reference-only');
        $parsed = $this->walker->parseSchema($schema, new Context());
        $this->assertEquals(2, count(get_object_vars($parsed)));
        $this->assertObjectHasAttribute('maximum', $parsed);
        $this->assertObjectHasAttribute('exclusiveMaximum', $parsed);
    }

    public function testApplyConstraintsWithRecursiveReference()
    {
        $schema = $this->loadSchema('valid/recursive-references');
        $this->walker->parseSchema($schema, new Context());

        $instance = new \stdClass();
        $instance->foo = new \stdClass();
        $instance->foo->foo = false;

        $this->walker->applyConstraints($instance, $schema, new Context());
    }
}
