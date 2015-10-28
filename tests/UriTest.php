<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal;

class UriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorThrowsOnInvalidUri()
    {
        new Uri(':');
    }

    /**
     * @dataProvider absoluteAndRelativeProvider
     *
     * @param string    $uri
     * @param bool      $isAbsolute
     */
    public function testIsAbsolute($uri, $isAbsolute)
    {
        $pointer = new Uri($uri);
        $this->assertEquals($isAbsolute, $pointer->isAbsolute());
    }

    /**
     * @dataProvider uriSegmentsProvider
     *
     * @param $uri
     * @param array $expectedSegments
     */
    public function testPointerSegments($uri, array $expectedSegments)
    {
        $pointer = new Uri($uri);
        $this->assertEquals($expectedSegments, $pointer->getPointerSegments());
    }

    /**
     * @expectedException \LogicException
     */
    public function testCannotResolveAgainstOtherUriIfAlreadyAbsolute()
    {
        $pointer = new Uri('http://localhost');
        $pointer->resolveAgainst(new Uri('file:///foo'));
    }

    /**
     * @expectedException \LogicException
     */
    public function testCannotResolveAgainstRelativeUri()
    {
        $pointer = new Uri('foo/bar');
        $pointer->resolveAgainst(new Uri('baz/quz'));
    }

    /**
     * @dataProvider againstUriProvider
     *
     * @param string $uri
     * @param string $againstUri
     * @param string $expectedResolved
     */
    public function testResolveAgainstAnotherUri($uri, $againstUri, $expectedResolved)
    {
        $pointer = new Uri($uri);
        $resolved = $pointer->resolveAgainst(new Uri($againstUri));
        $this->assertEquals($expectedResolved, $resolved);
    }

    public function testResolveAgainstUriChangesInternalState()
    {
        $pointer = new Uri('#quz/123');
        $against = new Uri('http://localhost:1234/foo/bar#baz');
        $pointer->resolveAgainst($against);
        $this->assertEquals('http://localhost:1234/foo/bar#quz/123', $pointer->getRawUri());
        $this->assertEquals('http', $pointer->getScheme());
        $this->assertEquals('http://localhost:1234/foo/bar', $pointer->getPrimaryResourceIdentifier());
        $this->assertEquals(['quz', '123'], $pointer->getPointerSegments());
    }

    /**
     * @expectedException \LogicException
     */
    public function testIsSamePrimaryResourceThrowsIfUrisAreNotAbsolute()
    {
        $pointer = new Uri('http://localhost');
        $against = new Uri('foo.bar/baz');
        $pointer->isSamePrimaryResource($against);
    }

    /**
     * @dataProvider sameResourceProvider
     *
     * @param string    uri
     * @param string    $againstUri
     * @param bool      $isSame
     */
    public function testIsSamePrimaryResource($uri, $againstUri, $isSame)
    {
        $pointer = new Uri($uri);
        $against = new Uri($againstUri);
        $this->assertEquals($isSame, $pointer->isSamePrimaryResource($against));
    }

    public function absoluteAndRelativeProvider()
    {
        return [
            ['http://localhost?123', true],
            ['file://foo/bar', true],
            ['//foo.bar', false],
            ['#/foo/bar', false]
        ];
    }

    public function uriSegmentsProvider()
    {
        return [
            ['http://localhost', []],
            ['file:///foo/bar', []],
            ['http://foo.bar/baz?foo=bar#/foo/bar', ['foo', 'bar']],
            ['//localhost/foo#/bar/baz', ['bar', 'baz']],
            ['/quz', []],
            ['/quz/#//', []],
            ['/quz/#//foo/1%25/bar', ['foo', '1%', 'bar']],
            ['/quz/#//foo/1~02/bar', ['foo', '1~2', 'bar']],
            ['/quz/#//foo/1~12/bar', ['foo', '1/2', 'bar']]
        ];
    }

    public function againstUriProvider()
    {
        return [
            ['foo.json', 'http://localhost/bar', 'http://localhost/foo.json'],
            ['foo.json', 'http://localhost/bar/baz', 'http://localhost/bar/foo.json'],
            ['/foo.json', 'http://localhost/bar/baz', 'http://localhost/foo.json'],
            ['/foo.json#/baz', 'http://localhost/bar', 'http://localhost/foo.json#/baz'],
            ['/foo.json#/baz', 'http://localhost:1234/bar', 'http://localhost:1234/foo.json#/baz'],
            ['#/baz/quz', 'http://localhost/bar?a=b#foo/1', 'http://localhost/bar?a=b#/baz/quz'],
            ['#/baz/quz', 'file:///foo/bar', 'file:///foo/bar#/baz/quz'],
            ['baz#/baz/quz', 'file:///foo/bar#baz', 'file:///foo/baz#/baz/quz'],
            ['/baz#/baz/quz', 'file:///foo/bar#baz', 'file:///baz#/baz/quz'],
            ['//quz', 'http://foo/bar', 'http://quz'],
            ['//quz/baz#/baz', 'http://foo/bar#baz', 'http://quz/baz#/baz'],
            ['?foo=a#/baz', 'http://localhost/bar?foo=b#baz', 'http://localhost/bar?foo=a#/baz'],
            ['//john:123@localhost', 'http://localhost:456/bar', 'http://john:123@localhost']
        ];
    }

    public function sameResourceProvider()
    {
        return [
            ['http://localhost', 'http://localhost', true],
            ['file://foo/bar#/baz', 'file://foo/bar#/baz/quz', true],
            ['http://localhost?a=b', 'http://localhost', false],
            ['file:///foo/bar', 'http://foo/bar', false],
        ];
    }
}
