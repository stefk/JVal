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
     * @dataProvider chainProvider
     * @param stdClass $schema
     * @param string    $pointerUri
     * @param stdClass $resolved
     */
    public function testResolveChain(stdClass $schema, $pointerUri, stdClass $resolved)
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

    /**
     * @dataProvider unfetchableUriProvider
     * @expectedException \JsonSchema\Exception\ResolverException
     * @expectedExceptionCode 18
     * @param string $pointerUri
     */
    public function testResolveThrowsOnUnfetchableUri($pointerUri)
    {
        $resolver = new Resolver();
        $resolver->pushSchema(new stdClass(), 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $resolver->resolve($reference);
    }

    /**
     * @dataProvider remoteUriProvider
     * @param string    $pointerUri
     * @param stdClass  $expectedSchema
     */
    public function testResolveRemoteSchema($pointerUri, stdClass $expectedSchema)
    {
        $resolver = new Resolver();
        $resolver->pushSchema(new stdClass(), 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $resolved = $resolver->resolve($reference);
        $this->assertEquals($expectedSchema, $resolved);
    }

    /**
     * @expectedException \JsonSchema\Exception\ResolverException
     * @expectedExceptionCode 19
     */
    public function testResolveThrowsOnUndecodableRemoteSchema()
    {
        $resolver = new Resolver();
        $resolver->pushSchema(new stdClass(), 'file:///foo/bar');
        $schemaFile = __DIR__.'/Data/invalid/undecodable.json';
        $reference = new stdClass();
        $reference->{'$ref'} = "file://{$schemaFile}";
        $resolver->resolve($reference);
    }

    /**
     * @expectedException \JsonSchema\Exception\ResolverException
     * @expectedExceptionCode 20
     */
    public function testResolveThrowsOnInvalidRemoteSchema()
    {
        $resolver = new Resolver();
        $resolver->pushSchema(new stdClass(), 'file:///foo/bar');
        $schemaFile = __DIR__.'/Data/invalid/not-an-object.json';
        $reference = new stdClass();
        $reference->{'$ref'} = "file://{$schemaFile}";
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

    public function chainProvider()
    {
        $schema = new stdClass();
        $schema->foo = new stdClass();
        $schema->bar = new stdClass();
        $schema->foo->baz = new stdClass();
        $schema->bar->baz = new stdClass();
        $schema->bar->baz->bat = new stdClass();
        $schema->bat = [];
        $schema->bat[0] = new stdClass();
        $schema->bat[1] = new stdClass();
        $schema->bat[1]->quz = [];
        $schema->bat[1]->quz[0] = new stdClass();
        $schema->{'with%percent'} = new stdClass();
        $schema->bar->{'with/slash'} = new stdClass();
        $schema->bar->{'with~tilde'} = new stdClass();

        return [
            [$schema, '#foo', $schema->foo],
            [$schema, '#/foo', $schema->foo],
            [$schema, '#/foo/baz', $schema->foo->baz],
            [$schema, '#/bar/baz', $schema->bar->baz],
            [$schema, '#/bar/baz/bat', $schema->bar->baz->bat],
            [$schema, '#/bar/baz/bat/', $schema->bar->baz->bat],
            [$schema, '#/bat/1', $schema->bat[0]],
            [$schema, '#/bat/2/quz/1', $schema->bat[1]->quz[0]],
            [$schema, '#/with%25percent', $schema->{'with%percent'}],
            [$schema, '#/bar/with~1slash', $schema->bar->{'with/slash'}],
            [$schema, '#/bar/with~1slash', $schema->bar->{'with/slash'}],
            [$schema, '#/bar/with~0tilde', $schema->bar->{'with~tilde'}]
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

    public function unfetchableUriProvider()
    {
        return [
            ['`malformed:/?u?r::l'],
            ['unknown://scheme'],
            ['http://non.existent/host'],
            ['http://localhost/non/existent/resource'],
            ['http://localhost/same#/with/pointer']
        ];
    }

    public function remoteUriProvider()
    {
        $schemaDir = $this->getVendorDir().'/json-schema/json-schema';
        $schemaFile = $schemaDir.'/draft-03/schema';
        $schema3 = json_decode(file_get_contents($schemaFile));

        return [
            ['http://json-schema.org/draft-03/schema#', $schema3],
            ["file://{$schemaFile}", $schema3]
        ];
    }

    private function getVendorDir()
    {
        $local = __DIR__.'/../vendor';
        $parent = __DIR__.'/../../../../vendor';

        if (is_dir($local)) {
            return $local;
        }

        if (is_dir($parent)) {
            return $parent;
        }

        throw new \Exception('Cannot find vendor dir');
    }
}
