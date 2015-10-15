<?php

namespace JsonSchema;

use stdClass;

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
        $schema = new stdClass();
        $resolver->pushSchema($schema, 'file:///foo/bar');
        $resolver->pushSchema($schema, 'file:///foo/bar');
    }

    /**
     * @dataProvider rootRefProvider
     * @param string $pointerUri
     */
    public function testResolveLocalRoot($pointerUri)
    {
        $resolver = new Resolver();
        $schema = new stdClass();
        $resolver->pushSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $resolved = $resolver->resolve($reference);
        $this->assertSame($schema, $resolved);
    }

    /**
     * @dataProvider chainPropertyProvider
     * @param stdClass $schema
     * @param string    $pointerUri
     * @param stdClass $resolved
     */
    public function testResolvePropertyChain(stdClass $schema, $pointerUri, stdClass $resolved)
    {
        $resolver = new Resolver();
        $resolver->pushSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $actual = $resolver->resolve($reference);
        $this->assertSame($actual, $resolved);
    }

    /**
     * @dataProvider unresolvablePointerPropertyProvider
     * @expectedException \JsonSchema\Exception\ResolverException
     * @expectedExceptionCode 12
     * @param stdClass $schema
     * @param string    $pointerUri
     */
    public function testResolveThrowsOnUnresolvedPointerProperty(stdClass $schema, $pointerUri)
    {
        $resolver = new Resolver();
        $resolver->pushSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $resolver->resolve($reference);
    }

    /**
     * @dataProvider invalidPointerIndexProvider
     * @expectedException \JsonSchema\Exception\ResolverException
     * @expectedExceptionCode 13
     * @param stdClass $schema
     * @param string    $pointerUri
     */
    public function testResolveThrowsOnInvalidPointerIndex(stdClass $schema, $pointerUri)
    {
        $resolver = new Resolver();
        $resolver->pushSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $resolver->resolve($reference);
    }

    /**
     * @dataProvider unresolvablePointerIndexProvider
     * @expectedException \JsonSchema\Exception\ResolverException
     * @expectedExceptionCode 14
     * @param stdClass $schema
     * @param string    $pointerUri
     */
    public function testResolveThrowsOnUnresolvedPointerIndex(stdClass $schema, $pointerUri)
    {
        $resolver = new Resolver();
        $resolver->pushSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $resolver->resolve($reference);
    }

    /**
     * @dataProvider invalidPointerSegmentProvider
     * @expectedException \JsonSchema\Exception\ResolverException
     * @expectedExceptionCode 15
     * @param stdClass $schema
     * @param string    $pointerUri
     */
    public function testResolveThrowsOnInvalidPointerSegment(stdClass $schema, $pointerUri)
    {
        $resolver = new Resolver();
        $resolver->pushSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $resolver->resolve($reference);
    }

    /**
     * @dataProvider invalidPointerTargetProvider
     * @expectedException \JsonSchema\Exception\ResolverException
     * @expectedExceptionCode 16
     * @param stdClass $schema
     * @param string    $pointerUri
     */
    public function testResolveThrowsOnInvalidPointerTarget(stdClass $schema, $pointerUri)
    {
        $resolver = new Resolver();
        $resolver->pushSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $resolver->resolve($reference);
    }

    /**
     * @dataProvider selfReferencingPointerProvider
     * @expectedException \JsonSchema\Exception\ResolverException
     * @expectedExceptionCode 17
     * @param stdClass $schema
     * @param stdClass $reference
     */
    public function testResolveThrowsOnSelfReferencingPointer(stdClass $schema, stdClass $reference)
    {
        $resolver = new Resolver();
        $resolver->pushSchema($schema, 'file:///foo/bar');
        $resolver->resolve($reference);
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
        $schema = new stdClass();
        $schema->foo = new stdClass();
        $schema->bar = new stdClass();
        $schema->foo->baz = new stdClass();
        $schema->bar->baz = new stdClass();
        $schema->bar->baz->bat = new stdClass();

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
        $schema = new stdClass();
        $schema->foo = new stdClass();
        $schema->foo->bar = new stdClass();
        $schema->foo->bar->baz = new stdClass();

        return [
            [$schema, '#nope'],
            [$schema, '#/foo/nope'],
            [$schema, '#foo/bar/nope']
        ];
    }

    public function invalidPointerIndexProvider()
    {
        $schema = new stdClass();
        $schema->foo = [];
        $schema->foo[0] = new stdClass();
        $schema->foo[0]->bar = [new stdClass(), new stdClass()];

        return [
            [$schema, '#/foo/bar'],
            [$schema, '#/foo/1/bar/baz']
        ];
    }

    public function unresolvablePointerIndexProvider()
    {
        $schema = new stdClass();
        $schema->foo = [];
        $schema->foo[0] = new stdClass();
        $schema->foo[0]->bar = [new stdClass(), new stdClass()];

        return [
            [$schema, '#/foo/2'],
            [$schema, '#/foo/1/bar/3']
        ];
    }

    public function invalidPointerSegmentProvider()
    {
        $schema = new stdClass();
        $schema->foo = [];
        $schema->foo[0] = 'nope';
        $schema->foo[1] = new stdClass();
        $schema->foo[1]->bar = 123;

        return [
            [$schema, '#/foo/1/bar'],
            [$schema, '#/foo/2/bar/baz']
        ];
    }

    public function invalidPointerTargetProvider()
    {
        $schema = new stdClass();
        $schema->foo = new stdClass();
        $schema->bar = [];
        $schema->foo->baz = new stdClass();
        $schema->foo->baz->bat = 123;

        return [
            [$schema, '#/bar'],
            [$schema, '#foo/baz/bat']
        ];
    }

    public function selfReferencingPointerProvider()
    {
        $schema1 = new stdClass();
        $schema1->{'$ref'} = '#';

        $schema2 = new stdClass();
        $schema2->foo = new stdClass();
        $schema2->foo->bar = new stdClass();
        $schema2->foo->bar->{'$ref'} = '#/foo/bar';

        return [
            [$schema1, $schema1],
            [$schema2, $schema2->foo->bar]
        ];
    }
}
