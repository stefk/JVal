<?php

namespace JsonSchema;

use JsonSchema\Exception\ResolverException;
use JsonSchema\Testing\BaseTestCase;
use stdClass;

class ResolverTest extends BaseTestCase
{
    /**
     * @var Resolver
     */
    private $resolver;

    protected function setUp()
    {
        $this->resolver = new Resolver();
        $this->setExceptionClass('JsonSchema\Exception\ResolverException');
    }

    public function testGetSchemaThrowsIfNoBaseSchema()
    {
        $this->expectException(ResolverException::NO_BASE_SCHEMA);
        $this->resolver->getBaseSchema();
    }

    /**
     * @dataProvider rootRefProvider
     * @param string $schemaName
     */
    public function testResolveLocalRoot($schemaName)
    {
        $schema = $this->loadSchema($schemaName);
        $this->resolver->setBaseSchema($schema, 'file:///foo/bar');
        $resolved = $this->resolver->resolve($schema->foo->bar);
        $this->assertEquals($schema, $resolved);
    }

    /**
     * @dataProvider chainProvider
     * @param stdClass $schema
     * @param string    $pointerUri
     * @param stdClass $resolved
     */
    public function testResolveChain(stdClass $schema, $pointerUri, stdClass $resolved)
    {
        $this->resolver->setBaseSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $actual = $this->resolver->resolve($reference);
        $this->assertSame($actual, $resolved);
    }

    /**
     * @dataProvider unresolvablePointerPropertyProvider
     * @param stdClass $schema
     * @param string    $pointerUri
     */
    public function testResolveThrowsOnUnresolvedPointerProperty(stdClass $schema, $pointerUri)
    {
        $this->expectException(ResolverException::UNRESOLVED_POINTER_PROPERTY);
        $this->resolver->setBaseSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $this->resolver->resolve($reference);
    }

    /**
     * @dataProvider invalidPointerIndexProvider
     * @param stdClass $schema
     * @param string    $pointerUri
     */
    public function testResolveThrowsOnInvalidPointerIndex(stdClass $schema, $pointerUri)
    {
        $this->expectException(ResolverException::INVALID_POINTER_INDEX);
        $this->resolver->setBaseSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $this->resolver->resolve($reference);
    }

    /**
     * @dataProvider unresolvablePointerIndexProvider
     * @param stdClass $schema
     * @param string    $pointerUri
     */
    public function testResolveThrowsOnUnresolvedPointerIndex(stdClass $schema, $pointerUri)
    {
        $this->expectException(ResolverException::UNRESOLVED_POINTER_INDEX);
        $this->resolver->setBaseSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $this->resolver->resolve($reference);
    }

    /**
     * @dataProvider invalidPointerSegmentProvider
     * @param stdClass $schema
     * @param string    $pointerUri
     */
    public function testResolveThrowsOnInvalidPointerSegment(stdClass $schema, $pointerUri)
    {
        $this->expectException(ResolverException::INVALID_SEGMENT_TYPE);
        $this->resolver->setBaseSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $this->resolver->resolve($reference);
    }

    /**
     * @dataProvider invalidPointerTargetProvider
     * @param stdClass $schema
     * @param string    $pointerUri
     */
    public function testResolveThrowsOnInvalidPointerTarget(stdClass $schema, $pointerUri)
    {
        $this->expectException(ResolverException::INVALID_POINTER_TARGET);
        $this->resolver->setBaseSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $this->resolver->resolve($reference);
    }

    /**
     * @dataProvider selfReferencingPointerProvider
     * @param stdClass $schema
     * @param stdClass $reference
     */
    public function testResolveThrowsOnSelfReferencingPointer(stdClass $schema, stdClass $reference)
    {
        $this->expectException(ResolverException::SELF_REFERENCING_POINTER);
        $this->resolver->setBaseSchema($schema, 'file:///foo/bar');
        $this->resolver->resolve($reference);
    }

    /**
     * @group network
     * @dataProvider unfetchableUriProvider
     * @param string $pointerUri
     */
    public function testResolveThrowsOnUnfetchableUri($pointerUri)
    {
        $this->expectException(ResolverException::UNFETCHABLE_URI);
        $this->resolver->setBaseSchema(new stdClass(), 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $this->resolver->resolve($reference);
    }

    /**
     * @group network
     * @dataProvider remoteUriProvider
     * @param string    $pointerUri
     * @param stdClass  $expectedSchema
     */
    public function testResolveRemoteSchema($pointerUri, stdClass $expectedSchema)
    {
        $this->resolver->setBaseSchema(new stdClass(), 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $resolved = $this->resolver->resolve($reference);
        $this->assertEquals($expectedSchema, $resolved);
    }

    public function testResolveThrowsOnUndecodableRemoteSchema()
    {
        $this->expectException(ResolverException::JSON_DECODE_ERROR);
        $this->resolver->setBaseSchema(new stdClass(), 'file:///foo/bar');
        $schemaFile = __DIR__ . '/Data/schemas/undecodable.json';
        $reference = new stdClass();
        $reference->{'$ref'} = "file://{$schemaFile}";
        $this->resolver->resolve($reference);
    }

    public function testResolveThrowsOnInvalidRemoteSchema()
    {
        $this->expectException(ResolverException::INVALID_REMOTE_SCHEMA);
        $this->resolver->setBaseSchema(new stdClass(), 'file:///foo/bar');
        $schemaFile = __DIR__ . '/Data/schemas/not-an-object.json';
        $reference = new stdClass();
        $reference->{'$ref'} = "file://{$schemaFile}";
        $this->resolver->resolve($reference);
    }

    public function rootRefProvider()
    {
        return [
            ['root-reference-1'],
            ['root-reference-2'],
            ['root-reference-3']
        ];
    }

    public function chainProvider()
    {
        $schema = $this->loadSchema('resolution-chains');

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
        $schema = $this->loadSchema('resolution-chains');

        return [
            [$schema, '#nope'],
            [$schema, '#/foo/nope'],
            [$schema, '#bar/baz/nope']
        ];
    }

    public function invalidPointerIndexProvider()
    {
        $schema = $this->loadSchema('resolution-chains');

        return [
            [$schema, '#/bat/2/quz/bar'],
            [$schema, '#/bat/2/quz/1/bar/baz']
        ];
    }

    public function unresolvablePointerIndexProvider()
    {
        $schema = $this->loadSchema('resolution-chains');

        return [
            [$schema, '#/bat/4'],
            [$schema, '#/bat/7/quz/2/bar']
        ];
    }

    public function invalidPointerSegmentProvider()
    {
        $schema = $this->loadSchema('resolution-chains');

        return [
            [$schema, '#/bat/3/bar'],
            [$schema, '#/bat/2/quz/2/foo']
        ];
    }

    public function invalidPointerTargetProvider()
    {
        $schema = $this->loadSchema('resolution-chains');

        return [
            [$schema, '#/foo/bat'],
            [$schema, '#bat']
        ];
    }

    public function selfReferencingPointerProvider()
    {
        $schema1 = $this->loadSchema('self-referencing-pointer-1');
        $schema2 = $this->loadSchema('self-referencing-pointer-2');

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
        $schemaFile = $schemaDir . '/draft-03/schema';
        $schema3 = $this->loadJsonFromFile($schemaFile);

        return [
            ['http://json-schema.org/draft-03/schema#', $schema3],
            ["file://{$schemaFile}", $schema3]
        ];
    }

    private function getVendorDir()
    {
        $local = __DIR__ . '/../vendor';
        $parent = __DIR__ . '/../../../../vendor';

        if (is_dir($local)) {
            return $local;
        }

        if (is_dir($parent)) {
            return $parent;
        }

        throw new \Exception('Cannot find vendor dir');
    }
}
