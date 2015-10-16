<?php

namespace JsonSchema;

use JsonSchema\Exception\ResolverException;
use stdClass;

class ResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSchemaThrowsIfNoBaseSchema()
    {
        $this->setExpectedException(
            'JsonSchema\Exception\ResolverException',
            ResolverException::NO_BASE_SCHEMA
        );

        $resolver = new Resolver();
        $resolver->getBaseSchema();
    }

    /**
     * @dataProvider rootRefProvider
     * @param string $pointerUri
     */
    public function testResolveLocalRoot($pointerUri)
    {
        $resolver = new Resolver();
        $schema = new stdClass();
        $resolver->setBaseSchema($schema, 'file:///foo/bar');
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
        $resolver->setBaseSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $actual = $resolver->resolve($reference);
        $this->assertSame($actual, $resolved);
    }

    /**
     * @dataProvider unresolvablePointerPropertyProvider
     * @param stdClass $schema
     * @param string    $pointerUri
     */
    public function testResolveThrowsOnUnresolvedPointerProperty(stdClass $schema, $pointerUri)
    {
        $this->setExpectedException(
            'JsonSchema\Exception\ResolverException',
            ResolverException::UNRESOLVED_POINTER_PROPERTY
        );

        $resolver = new Resolver();
        $resolver->setBaseSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $resolver->resolve($reference);
    }

    /**
     * @dataProvider invalidPointerIndexProvider
     * @param stdClass $schema
     * @param string    $pointerUri
     */
    public function testResolveThrowsOnInvalidPointerIndex(stdClass $schema, $pointerUri)
    {
        $this->setExpectedException(
            'JsonSchema\Exception\ResolverException',
            ResolverException::INVALID_POINTER_INDEX
        );

        $resolver = new Resolver();
        $resolver->setBaseSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $resolver->resolve($reference);
    }

    /**
     * @dataProvider unresolvablePointerIndexProvider
     * @param stdClass $schema
     * @param string    $pointerUri
     */
    public function testResolveThrowsOnUnresolvedPointerIndex(stdClass $schema, $pointerUri)
    {
        $this->setExpectedException(
            'JsonSchema\Exception\ResolverException',
            ResolverException::UNRESOLVED_POINTER_INDEX
        );

        $resolver = new Resolver();
        $resolver->setBaseSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $resolver->resolve($reference);
    }

    /**
     * @dataProvider invalidPointerSegmentProvider
     * @param stdClass $schema
     * @param string    $pointerUri
     */
    public function testResolveThrowsOnInvalidPointerSegment(stdClass $schema, $pointerUri)
    {
        $this->setExpectedException(
            'JsonSchema\Exception\ResolverException',
            ResolverException::INVALID_SEGMENT_TYPE
        );

        $resolver = new Resolver();
        $resolver->setBaseSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $resolver->resolve($reference);
    }

    /**
     * @dataProvider invalidPointerTargetProvider
     * @param stdClass $schema
     * @param string    $pointerUri
     */
    public function testResolveThrowsOnInvalidPointerTarget(stdClass $schema, $pointerUri)
    {
        $this->setExpectedException(
            'JsonSchema\Exception\ResolverException',
            ResolverException::INVALID_POINTER_TARGET
        );

        $resolver = new Resolver();
        $resolver->setBaseSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $resolver->resolve($reference);
    }

    /**
     * @dataProvider selfReferencingPointerProvider
     * @param stdClass $schema
     * @param stdClass $reference
     */
    public function testResolveThrowsOnSelfReferencingPointer(stdClass $schema, stdClass $reference)
    {
        $this->setExpectedException(
            'JsonSchema\Exception\ResolverException',
            ResolverException::SELF_REFERENCING_POINTER
        );

        $resolver = new Resolver();
        $resolver->setBaseSchema($schema, 'file:///foo/bar');
        $resolver->resolve($reference);
    }

    /**
     * @group network
     * @dataProvider unfetchableUriProvider
     * @param string $pointerUri
     */
    public function testResolveThrowsOnUnfetchableUri($pointerUri)
    {
        $this->setExpectedException(
            'JsonSchema\Exception\ResolverException',
            ResolverException::UNFETCHABLE_URI
        );

        $resolver = new Resolver();
        $resolver->setBaseSchema(new stdClass(), 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $resolver->resolve($reference);
    }

    /**
     * @group network
     * @dataProvider remoteUriProvider
     * @param string    $pointerUri
     * @param stdClass  $expectedSchema
     */
    public function testResolveRemoteSchema($pointerUri, stdClass $expectedSchema)
    {
        $resolver = new Resolver();
        $resolver->setBaseSchema(new stdClass(), 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $resolved = $resolver->resolve($reference);
        $this->assertEquals($expectedSchema, $resolved);
    }

    public function testResolveThrowsOnUndecodableRemoteSchema()
    {
        $this->setExpectedException(
            'JsonSchema\Exception\ResolverException',
            ResolverException::JSON_DECODE_ERROR
        );

        $resolver = new Resolver();
        $resolver->setBaseSchema(new stdClass(), 'file:///foo/bar');
        $schemaFile = __DIR__.'/Data/invalid/undecodable.json';
        $reference = new stdClass();
        $reference->{'$ref'} = "file://{$schemaFile}";
        $resolver->resolve($reference);
    }

    public function testResolveThrowsOnInvalidRemoteSchema()
    {
        $this->setExpectedException(
            'JsonSchema\Exception\ResolverException',
            ResolverException::INVALID_REMOTE_SCHEMA
        );

        $resolver = new Resolver();
        $resolver->setBaseSchema(new stdClass(), 'file:///foo/bar');
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
