<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal;

use JVal\Testing\BaseTestCase;

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
        $resolver->setPreFetchHook(function ($uri) {
            return str_replace(
                'http://localhost:1234',
                $this->getLocalUri(__DIR__.'/Data/schemas'),
                $uri
            );
        });
        $this->walker = new Walker($registry, $resolver);
    }

    public function testResolveReferencesWithNoReferences()
    {
        $schema = $this->loadSchema('valid/items-array');
        $resolved = $this->walker->resolveReferences($schema, new Uri('file:///foo/bar'));
        $this->assertEquals($schema, $resolved);
        $this->assertSame($schema, $resolved);
    }

    public function testResolveReferencesWithLocalReferences()
    {
        $schema = $this->loadSchema('valid/local-references');
        $resolved = $this->walker->resolveReferences($schema, new Uri('file:///foo/bar'));
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
        $resolved = $this->walker->resolveReferences($schema, new Uri('file:///foo/bar'));

        $this->assertObjectHasAttribute('properties', $resolved);

        $this->assertObjectHasAttribute('foo', $resolved->properties);
        $this->assertObjectNotHasAttribute('$ref', $resolved->properties->foo);
        $this->assertSame($resolved, $resolved->properties->foo);

        $this->assertObjectHasAttribute('bar', $resolved->properties);
        $this->assertObjectNotHasAttribute('$ref', $resolved->properties->bar);
        $this->assertSame($resolved->properties->bar, $resolved->definitions->bar);

        $this->assertObjectHasAttribute('baz', $resolved->properties);
        $this->assertObjectNotHasAttribute('$ref', $resolved->properties->baz);
        $this->assertSame($resolved, $resolved->properties->baz);
    }

    public function testResolveReferencesWithOneAbsoluteRemoteReferenceOnly()
    {
        $schema = $this->loadSchema('valid/remote-absolute-reference');
        $resolved = $this->walker->resolveReferences($schema, new Uri('file:///foo/bar'));
        $this->assertEquals(1, count(get_object_vars($resolved)));
        $this->assertObjectHasAttribute('maximum', $resolved);
    }

    public function testResolveReferencesWithOneRelativeRemoteReferenceOnly()
    {
        $schema = $this->loadSchema('valid/remote-relative-reference');
        $resolved = $this->walker->resolveReferences($schema, new Uri('http://localhost:1234/valid/base.json'));
        $this->assertEquals(1, count(get_object_vars($resolved)));
        $this->assertObjectHasAttribute('minimum', $resolved);
    }

    public function testResolveReferencesWithScopeChanges()
    {
        $schema = $this->loadSchema('valid/scoped-references');
        $resolved = $this->walker->resolveReferences($schema, new Uri('file:///foo/bar'));

        $this->assertObjectHasAttribute('properties', $resolved);

        $this->assertObjectHasAttribute('foo', $resolved->properties);
        $this->assertObjectHasAttribute('items', $resolved->properties->foo);

        $this->assertObjectHasAttribute('bar', $resolved->properties);
        $this->assertObjectHasAttribute('minimum', $resolved->properties->bar);
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
