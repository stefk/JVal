<?php

namespace JsonSchema;

class ResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \JsonSchema\Exception\ResolverException
     * @expectedExceptionCode 10
     */
    public function testPopSchemaThrowsIfStackIsEmpty()
    {
        $resolver = new Resolver();
        $resolver->popSchema();
    }

    /**
     * @expectedException \JsonSchema\Exception\ResolverException
     * @expectedExceptionCode 10
     */
    public function testCurrentSchemaThrowsIfStackIsEmpty()
    {
        $resolver = new Resolver();
        $resolver->currentSchema();
    }

    /**
     * @expectedException \JsonSchema\Exception\ResolverException
     * @expectedExceptionCode 11
     */
    public function testPushSchemaThrowsIfUriIsAlreadyRegistered()
    {
        $resolver = new Resolver();
        $schema = new \stdClass();
        $resolver->pushSchema($schema, 'file:///foo/bar');
        $resolver->pushSchema($schema, 'file:///foo/bar');
    }

    /**
     * @dataProvider rootRefProvider
     * @param string $ref
     */
    public function testResolveLocalRoot($ref)
    {
        $resolver = new Resolver();
        $schema = new \stdClass();
        $resolver->pushSchema($schema, 'file:///foo/bar');
        $resolved = $resolver->resolve($ref);
        $this->assertSame($schema, $resolved);
    }

    /**
     * @dataProvider chainPropertyProvider
     * @param \stdClass $schema
     * @param string    $ref
     * @param \stdClass $resolved
     */
    public function testResolvePropertyChain(\stdClass $schema, $ref, \stdClass $resolved)
    {
        $resolver = new Resolver();
        $resolver->pushSchema($schema, 'file:///foo/bar');
        $actual = $resolver->resolve($ref);
        $this->assertSame($actual, $resolved);
    }

    /**
     * @dataProvider unresolvablePointerPropertyProvider
     * @expectedException \JsonSchema\Exception\ResolverException
     * @expectedExceptionCode 12
     * @param \stdClass $schema
     * @param string    $ref
     */
    public function testResolveThrowsOnUnresolvedPointerProperty(\stdClass $schema, $ref)
    {
        $resolver = new Resolver();
        $resolver->pushSchema($schema, 'file:///foo/bar');
        $resolver->resolve($ref);
    }

    /**
     * @dataProvider invalidPointerIndexProvider
     * @expectedException \JsonSchema\Exception\ResolverException
     * @expectedExceptionCode 13
     * @param \stdClass $schema
     * @param string    $ref
     */
    public function testResolveThrowsOnInvalidPointerIndex(\stdClass $schema, $ref)
    {
        $resolver = new Resolver();
        $resolver->pushSchema($schema, 'file:///foo/bar');
        $resolver->resolve($ref);
    }

    /**
     * @dataProvider unresolvablePointerIndexProvider
     * @expectedException \JsonSchema\Exception\ResolverException
     * @expectedExceptionCode 14
     * @param \stdClass $schema
     * @param string    $ref
     */
    public function testResolveThrowsOnUnresolvedPointerIndex(\stdClass $schema, $ref)
    {
        $resolver = new Resolver();
        $resolver->pushSchema($schema, 'file:///foo/bar');
        $resolver->resolve($ref);
    }

    /**
     * @dataProvider invalidPointerSegmentProvider
     * @expectedException \JsonSchema\Exception\ResolverException
     * @expectedExceptionCode 15
     * @param \stdClass $schema
     * @param string    $ref
     */
    public function testResolveThrowsOnInvalidPointerSegment(\stdClass $schema, $ref)
    {
        $resolver = new Resolver();
        $resolver->pushSchema($schema, 'file:///foo/bar');
        $resolver->resolve($ref);
    }

    /**
     * @dataProvider invalidPointerTargetProvider
     * @expectedException \JsonSchema\Exception\ResolverException
     * @expectedExceptionCode 16
     * @param \stdClass $schema
     * @param string    $ref
     */
    public function testResolveThrowsOnInvalidPointerTarget(\stdClass $schema, $ref)
    {
        $resolver = new Resolver();
        $resolver->pushSchema($schema, 'file:///foo/bar');
        $resolver->resolve($ref);
    }

    public function rootRefProvider()
    {
        return [
            ['#'],
            ['#/'],
            ['#///']
        ];
    }

    public function chainPropertyProvider()
    {
        $schema = new \stdClass();
        $schema->foo = new \stdClass();
        $schema->bar = new \stdClass();
        $schema->foo->baz = new \stdClass();
        $schema->bar->baz = new \stdClass();
        $schema->bar->baz->bat = new \stdClass();

        return [
            [$schema, '#foo', $schema->foo],
            [$schema, '#/foo', $schema->foo],
            [$schema, '#/foo/baz', $schema->foo->baz],
            [$schema, '#/bar/baz', $schema->bar->baz],
            [$schema, '#/bar/baz/bat', $schema->bar->baz->bat],
            [$schema, '#/bar/baz/bat/', $schema->bar->baz->bat]
        ];
    }

    public function unresolvablePointerPropertyProvider()
    {
        $schema = new \stdClass();
        $schema->foo = new \stdClass();
        $schema->foo->bar = new \stdClass();
        $schema->foo->bar->baz = new \stdClass();

        return [
            [$schema, '#nope'],
            [$schema, '#/foo/nope'],
            [$schema, '#foo/bar/nope']
        ];
    }

    public function invalidPointerIndexProvider()
    {
        $schema = new \stdClass();
        $schema->foo = [];
        $schema->foo[0] = new \stdClass();
        $schema->foo[0]->bar = [new \stdClass(), new \stdClass()];

        return [
            [$schema, '#/foo/bar'],
            [$schema, '#/foo/1/bar/baz']
        ];
    }

    public function unresolvablePointerIndexProvider()
    {
        $schema = new \stdClass();
        $schema->foo = [];
        $schema->foo[0] = new \stdClass();
        $schema->foo[0]->bar = [new \stdClass(), new \stdClass()];

        return [
            [$schema, '#/foo/2'],
            [$schema, '#/foo/1/bar/3']
        ];
    }

    public function invalidPointerSegmentProvider()
    {
        $schema = new \stdClass();
        $schema->foo = [];
        $schema->foo[0] = 'nope';
        $schema->foo[1] = new \stdClass();
        $schema->foo[1]->bar = 123;

        return [
            [$schema, '#/foo/1/bar'],
            [$schema, '#/foo/2/bar/baz']
        ];
    }

    public function invalidPointerTargetProvider()
    {
        $schema = new \stdClass();
        $schema->foo = new \stdClass();
        $schema->bar = [];
        $schema->foo->baz = new \stdClass();
        $schema->foo->baz->bat = 123;

        return [
            [$schema, '#/bar'],
            [$schema, '#foo/baz/bat']
        ];
    }
}
