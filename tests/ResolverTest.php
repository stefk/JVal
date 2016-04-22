<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal;

use JVal\Testing\BaseTestCase;
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
    }

    public function testBasicStackHandling()
    {
        $schemaA = new stdClass();
        $uriA = new Uri('file:///foo/bar/a');
        $this->resolver->initialize($schemaA, $uriA);

        $this->assertSame($schemaA, $this->resolver->getRootSchema());
        $this->assertSame($uriA, $this->resolver->getRootUri());
        $this->assertSame($uriA, $this->resolver->getCurrentUri());

        $schemaB = new stdClass();
        $uriB = new Uri('file:///foo/bar/b');
        $this->resolver->enter($uriB, $schemaB);
        $this->assertSame($schemaA, $this->resolver->getRootSchema());
        $this->assertSame($uriA, $this->resolver->getRootUri());
        $this->assertSame($uriB, $this->resolver->getCurrentUri());
    }

    /**
     * @expectedException \JVal\Exception\Resolver\EmptyStackException
     */
    public function testGetRootUriThrowsIfStackIsEmpty()
    {
        $this->resolver->getRootUri();
    }

    /**
     * @expectedException \JVal\Exception\Resolver\EmptyStackException
     */
    public function testGetCurrentUriThrowsIfStackIsEmpty()
    {
        $this->resolver->getCurrentUri();
    }

    /**
     * @expectedException \JVal\Exception\Resolver\EmptyStackException
     */
    public function testLeaveThrowsIfStackIsEmpty()
    {
        $this->resolver->leave();
    }

    /**
     * @dataProvider rootRefProvider
     *
     * @param string $schemaName
     */
    public function testResolveLocalRoot($schemaName)
    {
        $schema = $this->loadSchema($schemaName);
        $this->resolver->initialize($schema, new Uri('file:///foo/bar'));
        $resolved = $this->resolver->resolve($schema->foo->bar);
        $this->assertEquals($schema, $resolved[1]);
    }

    /**
     * @dataProvider chainProvider
     *
     * @param stdClass $schema
     * @param string   $pointerUri
     * @param stdClass $resolved
     */
    public function testResolveChain(stdClass $schema, $pointerUri, stdClass $resolved)
    {
        $this->resolver->initialize($schema, new Uri('file:///foo/bar'));
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $actual = $this->resolver->resolve($reference);
        $this->assertSame($actual[1], $resolved);
    }

    /**
     * @dataProvider chainProvider
     *
     * @param stdClass $schema
     * @param string   $pointerUri
     * @param stdClass $resolved
     */
    public function testResolveChainWithoutAbsoluteUri(stdClass $schema, $pointerUri, stdClass $resolved)
    {
        $this->resolver->initialize($schema, new Uri(''));
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $actual = $this->resolver->resolve($reference);
        $this->assertSame($actual[1], $resolved);
    }

    /**
     * @dataProvider unresolvablePointerPropertyProvider
     * @expectedException \JVal\Exception\Resolver\UnresolvedPointerPropertyException
     *
     * @param stdClass $schema
     * @param string   $pointerUri
     */
    public function testResolveThrowsOnUnresolvedPointerProperty(stdClass $schema, $pointerUri)
    {
        $this->resolver->initialize($schema, new Uri('file:///foo/bar'));
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $this->resolver->resolve($reference);
    }

    /**
     * @dataProvider invalidPointerIndexProvider
     * @expectedException \JVal\Exception\Resolver\InvalidPointerIndexException
     *
     * @param stdClass $schema
     * @param string   $pointerUri
     */
    public function testResolveThrowsOnInvalidPointerIndex(stdClass $schema, $pointerUri)
    {
        $this->resolver->initialize($schema, new Uri('file:///foo/bar'));
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $this->resolver->resolve($reference);
    }

    /**
     * @dataProvider unresolvablePointerIndexProvider
     * @expectedException \JVal\Exception\Resolver\UnresolvedPointerIndexException
     *
     * @param stdClass $schema
     * @param string   $pointerUri
     */
    public function testResolveThrowsOnUnresolvedPointerIndex(stdClass $schema, $pointerUri)
    {
        $this->resolver->initialize($schema, new Uri('file:///foo/bar'));
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $this->resolver->resolve($reference);
    }

    /**
     * @dataProvider invalidPointerSegmentProvider
     * @expectedException \JVal\Exception\Resolver\InvalidSegmentTypeException
     *
     * @param stdClass $schema
     * @param string   $pointerUri
     */
    public function testResolveThrowsOnInvalidPointerSegment(stdClass $schema, $pointerUri)
    {
        $this->resolver->initialize($schema, new Uri('file:///foo/bar'));
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $this->resolver->resolve($reference);
    }

    /**
     * @dataProvider invalidPointerTargetProvider
     * @expectedException \JVal\Exception\Resolver\InvalidPointerTargetException
     *
     * @param stdClass $schema
     * @param string   $pointerUri
     */
    public function testResolveThrowsOnInvalidPointerTarget(stdClass $schema, $pointerUri)
    {
        $this->resolver->initialize($schema, new Uri('file:///foo/bar'));
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $this->resolver->resolve($reference);
    }

    /**
     * @dataProvider selfReferencingPointerProvider
     * @expectedException \JVal\Exception\Resolver\SelfReferencingPointerException
     *
     * @param stdClass $schema
     * @param stdClass $reference
     */
    public function testResolveThrowsOnSelfReferencingPointer(stdClass $schema, stdClass $reference)
    {
        $this->resolver->initialize($schema, new Uri('file:///foo/bar'));
        $this->resolver->resolve($reference);
    }

    /**
     * @group network
     * @dataProvider unfetchableUriProvider
     * @expectedException \JVal\Exception\Resolver\UnfetchableUriException
     *
     * @param string $pointerUri
     */
    public function testResolveThrowsOnUnfetchableUri($pointerUri)
    {
        $this->resolver->initialize(new stdClass(), new Uri('file:///foo/bar'));
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $this->resolver->resolve($reference);
    }

    /**
     * @group network
     * @dataProvider remoteUriProvider
     *
     * @param string   $pointerUri
     * @param stdClass $expectedSchema
     */
    public function testResolveRemoteSchema($pointerUri, stdClass $expectedSchema)
    {
        $this->resolver->initialize(new stdClass(), new Uri('file:///foo/bar'));
        $reference = new stdClass();
        $reference->{'$ref'} = $pointerUri;
        $resolved = $this->resolver->resolve($reference);
        $this->assertEquals($resolved[1], $expectedSchema);
    }

    /**
     * @expectedException \JVal\Exception\JsonDecodeException
     */
    public function testResolveThrowsOnUndecodableRemoteSchema()
    {
        $this->resolver->initialize(new stdClass(), new Uri('file:///foo/bar'));
        $schemaFile = __DIR__.'/Data/schemas/invalid/undecodable.json';
        $reference = new stdClass();
        $reference->{'$ref'} = $this->getLocalUri($schemaFile);
        $this->resolver->resolve($reference);
    }

    /**
     * @expectedException \JVal\Exception\Resolver\InvalidRemoteSchemaException
     */
    public function testResolveThrowsOnInvalidRemoteSchema()
    {
        $this->resolver->initialize(new stdClass(), new Uri('file:///foo/bar'));
        $schemaFile = __DIR__.'/Data/schemas/invalid/not-an-object.json';
        $reference = new stdClass();
        $reference->{'$ref'} = $this->getLocalUri($schemaFile);
        $this->resolver->resolve($reference);
    }

    public function rootRefProvider()
    {
        return [
            ['valid/root-reference'],
        ];
    }

    public function chainProvider()
    {
        $schema = $this->loadSchema('valid/resolution-chains');

        return [
            [$schema, '#/foo', $schema->foo],
            [$schema, '#/foo/baz', $schema->foo->baz],
            [$schema, '#/bar/baz', $schema->bar->baz],
            [$schema, '#/bar/baz/bat', $schema->bar->baz->bat],
            [$schema, '#/bat/0', $schema->bat[0]],
            [$schema, '#/bat/1/quz/0', $schema->bat[1]->quz[0]],
            [$schema, '#/with%25percent', $schema->{'with%percent'}],
            [$schema, '#/bar/with~1slash', $schema->bar->{'with/slash'}],
            [$schema, '#/bar/with~1slash', $schema->bar->{'with/slash'}],
            [$schema, '#/bar/with~0tilde', $schema->bar->{'with~tilde'}],
        ];
    }

    public function unresolvablePointerPropertyProvider()
    {
        $schema = $this->loadSchema('valid/resolution-chains');

        return [
            [$schema, '#/nope'],
            [$schema, '#/foo/nope'],
            [$schema, '#/bar/baz/nope'],
        ];
    }

    public function invalidPointerIndexProvider()
    {
        $schema = $this->loadSchema('valid/resolution-chains');

        return [
            [$schema, '#/bat/1/quz/bar'],
            [$schema, '#/bat/1/quz/0/bar/baz'],
        ];
    }

    public function unresolvablePointerIndexProvider()
    {
        $schema = $this->loadSchema('valid/resolution-chains');

        return [
            [$schema, '#/bat/4'],
            [$schema, '#/bat/7/quz/2/bar'],
        ];
    }

    public function invalidPointerSegmentProvider()
    {
        $schema = $this->loadSchema('valid/resolution-chains');

        return [
            [$schema, '#/bat/2/bar'],
            [$schema, '#/bat/1/quz/1/foo'],
        ];
    }

    public function invalidPointerTargetProvider()
    {
        $schema = $this->loadSchema('valid/resolution-chains');

        return [
            [$schema, '#/foo/bat'],
            [$schema, '#/bat'],
        ];
    }

    public function selfReferencingPointerProvider()
    {
        $schema1 = $this->loadSchema('invalid/self-referencing-pointer-1');
        $schema2 = $this->loadSchema('invalid/self-referencing-pointer-2');

        return [
            [$schema1, $schema1],
            [$schema2, $schema2->foo->bar],
        ];
    }

    public function unfetchableUriProvider()
    {
        return [
            ['`malformed:/?u?r::l'],
            ['unknown://scheme'],
            ['http://non.existent/host'],
            ['http://localhost/non/existent/resource'],
            ['http://localhost/same#/with/pointer'],
        ];
    }

    public function remoteUriProvider()
    {
        $schemaDir = $this->getVendorDir().'/json-schema/json-schema';
        $schemaFile = $schemaDir.'/draft-03/schema';
        $schema3 = $this->loadJsonFromFile($schemaFile);

        return [
            ['http://json-schema.org/draft-03/schema#', $schema3],
            [$this->getLocalUri($schemaFile), $schema3],
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
