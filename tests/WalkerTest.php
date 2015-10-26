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

    public function testResolveReferencesWithNoReferences()
    {
        $schema = $this->loadSchema('valid/items-array');
        $resolved = $this->walker->resolveReferences($schema, 'file:///foo/bar');
        $this->assertEquals($schema, $resolved);
        $this->assertSame($schema, $resolved);
    }

    public function testResolveReferencesWithLocalReferences()
    {
        $schema = $this->loadSchema('valid/local-references');
        $resolved = $this->walker->resolveReferences($schema, 'file:///foo/bar');
        $this->assertObjectHasAttribute('properties', $resolved);

        $this->assertObjectHasAttribute('foo', $resolved->properties);
        $this->assertObjectNotHasAttribute('$ref', $resolved->properties->foo);
        $this->assertSame($schema->definitions->foo, $resolved->properties->foo);

        $this->assertObjectHasAttribute('bar', $resolved->properties);
        $this->assertObjectNotHasAttribute('$ref', $resolved->properties->bar);
        $this->assertSame($schema->definitions->bar, $resolved->properties->bar);

        $this->assertObjectHasAttribute('items', $resolved);
        $this->assertArrayHasKey(0, $resolved->items);
        $this->assertSame($schema->definitions->baz[0], $resolved->items[0]);
    }

    public function testResolveReferencesWithRecursiveReferences()
    {
        $schema = $this->loadSchema('valid/recursive-references');
        $resolved = $this->walker->resolveReferences($schema, 'file:///foo/bar');

        $this->assertObjectHasAttribute('properties', $resolved);

        $this->assertObjectHasAttribute('foo', $resolved->properties);
        $this->assertObjectNotHasAttribute('$ref', $resolved->properties->foo);
        $this->assertSame($resolved, $resolved->properties->foo);

        $this->assertObjectHasAttribute('bar', $resolved->properties);
        $this->assertObjectNotHasAttribute('$ref', $resolved->properties->bar);
        $this->assertSame($resolved->properties->bar, $resolved->definitions->bar);
    }

    public function testResolveReferencesWithOneAbsoluteRemoteReferenceOnly()
    {
        $schema = $this->loadSchema('valid/remote-absolute-reference');
        $resolved = $this->walker->resolveReferences($schema, 'file:///foo/bar');
        $this->assertEquals(1, count(get_object_vars($resolved)));
        $this->assertObjectHasAttribute('maximum', $resolved);
    }

    public function testResolveReferencesWithOneARelativeRemoteReferenceOnly()
    {
        $schema = $this->loadSchema('valid/remote-relative-reference');
        $resolved = $this->walker->resolveReferences($schema, 'http://localhost:1342/base.json');
        $this->assertEquals(1, count(get_object_vars($resolved)));
        $this->assertObjectHasAttribute('maximum', $resolved);
    }
//
//    public function testApplyConstraintsWithRecursiveReference()
//    {
//        $schema = $this->loadSchema('valid/recursive-references');
//        $this->walker->parseSchema($schema, new Context());
//
//        $instance = new \stdClass();
//        $instance->foo = new \stdClass();
//        $instance->foo->foo = false;
//
//        $this->walker->applyConstraints($instance, $schema, new Context());
//    }
}
