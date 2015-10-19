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

    /**
     * @expectedException \JsonSchema\Exception\Resolver\NoBaseSchemaException
     */
    public function testGetSchemaThrowsIfNoBaseSchema()
    {
        $this->resolver->getBaseSchema();
    }

    /**
     * @dataProvider rootRefProvider
     *
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
     *
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
     * @expectedException \JsonSchema\Exception\Resolver\UnresolvedPointerPropertyException
     *
     * @param stdClass $schema
     * @param string    $pointerUri
     */
    public function testResolveThrowsOnUnresolvedPointerProperty(stdClass $schema, $pointerUri)
    {
        $this->resolver->setBaseSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $this->resolver->resolve($reference);
    }

    /**
     * @dataProvider invalidPointerIndexProvider
     * @expectedException \JsonSchema\Exception\Resolver\InvalidPointerIndexException
     *
     * @param stdClass $schema
     * @param string    $pointerUri
     */
    public function testResolveThrowsOnInvalidPointerIndex(stdClass $schema, $pointerUri)
    {
        $this->resolver->setBaseSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $this->resolver->resolve($reference);
    }

    /**
     * @dataProvider unresolvablePointerIndexProvider
     * @expectedException \JsonSchema\Exception\Resolver\UnresolvedPointerIndexException
     *
     * @param stdClass $schema
     * @param string    $pointerUri
     */
    public function testResolveThrowsOnUnresolvedPointerIndex(stdClass $schema, $pointerUri)
    {
        $this->resolver->setBaseSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $this->resolver->resolve($reference);
    }

    /**
     * @dataProvider invalidPointerSegmentProvider
     * @expectedException \JsonSchema\Exception\Resolver\InvalidSegmentTypeException
     *
     * @param stdClass $schema
     * @param string    $pointerUri
     */
    public function testResolveThrowsOnInvalidPointerSegment(stdClass $schema, $pointerUri)
    {
        $this->resolver->setBaseSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $this->resolver->resolve($reference);
    }

    /**
     * @dataProvider invalidPointerTargetProvider
     * @expectedException \JsonSchema\Exception\Resolver\InvalidPointerTargetException
     *
     * @param stdClass $schema
     * @param string    $pointerUri
     */
    public function testResolveThrowsOnInvalidPointerTarget(stdClass $schema, $pointerUri)
    {
        $this->resolver->setBaseSchema($schema, 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $this->resolver->resolve($reference);
    }

    /**
     * @dataProvider selfReferencingPointerProvider
     * @expectedException \JsonSchema\Exception\Resolver\SelfReferencingPointerException
     *
     * @param stdClass $schema
     * @param stdClass $reference
     */
    public function testResolveThrowsOnSelfReferencingPointer(stdClass $schema, stdClass $reference)
    {
        $this->resolver->setBaseSchema($schema, 'file:///foo/bar');
        $this->resolver->resolve($reference);
    }

    /**
     * @group network
     * @dataProvider unfetchableUriProvider
     * @expectedException \JsonSchema\Exception\Resolver\UnfetchableUriException
     *
     * @param string $pointerUri
     */
    public function testResolveThrowsOnUnfetchableUri($pointerUri)
    {
        $this->resolver->setBaseSchema(new stdClass(), 'file:///foo/bar');
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $this->resolver->resolve($reference);
    }

    /**
     * @group network
     * @dataProvider remoteUriProvider
     *
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

    /**
     * @expectedException \JsonSchema\Exception\Resolver\JsonDecodeErrorException
     */
    public function testResolveThrowsOnUndecodableRemoteSchema()
    {
        $this->resolver->setBaseSchema(new stdClass(), 'file:///foo/bar');
        $schemaFile = __DIR__ . '/Data/schemas/invalid/undecodable.json';
        $reference = new stdClass();
        $reference->{'$ref'} = "file://{$schemaFile}";
        $this->resolver->resolve($reference);
    }

    /**
     * @expectedException \JsonSchema\Exception\Resolver\InvalidRemoteSchemaException
     */
    public function testResolveThrowsOnInvalidRemoteSchema()
    {
        $this->resolver->setBaseSchema(new stdClass(), 'file:///foo/bar');
        $schemaFile = __DIR__ . '/Data/schemas/invalid/not-an-object.json';
        $reference = new stdClass();
        $reference->{'$ref'} = "file://{$schemaFile}";
        $this->resolver->resolve($reference);
    }

    public function rootRefProvider()
    {
        return [
            ['valid/root-reference-1'],
            ['valid/root-reference-2'],
            ['valid/root-reference-3']
        ];
    }

    public function chainProvider()
    {
        $schema = $this->loadSchema('valid/resolution-chains');

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
        $schema = $this->loadSchema('valid/resolution-chains');

        return [
            [$schema, '#nope'],
            [$schema, '#/foo/nope'],
            [$schema, '#bar/baz/nope']
        ];
    }

    public function invalidPointerIndexProvider()
    {
        $schema = $this->loadSchema('valid/resolution-chains');

        return [
            [$schema, '#/bat/2/quz/bar'],
            [$schema, '#/bat/2/quz/1/bar/baz']
        ];
    }

    public function unresolvablePointerIndexProvider()
    {
        $schema = $this->loadSchema('valid/resolution-chains');

        return [
            [$schema, '#/bat/4'],
            [$schema, '#/bat/7/quz/2/bar']
        ];
    }

    public function invalidPointerSegmentProvider()
    {
        $schema = $this->loadSchema('valid/resolution-chains');

        return [
            [$schema, '#/bat/3/bar'],
            [$schema, '#/bat/2/quz/2/foo']
        ];
    }

    public function invalidPointerTargetProvider()
    {
        $schema = $this->loadSchema('valid/resolution-chains');

        return [
            [$schema, '#/foo/bat'],
            [$schema, '#bat']
        ];
    }

    public function selfReferencingPointerProvider()
    {
        $schema1 = $this->loadSchema('invalid/self-referencing-pointer-1');
        $schema2 = $this->loadSchema('invalid/self-referencing-pointer-2');

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
