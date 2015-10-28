<?php

namespace JVal;

class UtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider equalDataProvider
     *
     * @param mixed $a
     * @param mixed $b
     */
    public function testAreEqualWithEqualData($a, $b)
    {
        $this->assertTrue(Utils::areEqual($a, $b));
    }

    /**
     * @dataProvider notEqualDataProvider
     *
     * @param mixed $a
     * @param mixed $b
     */
    public function testAreEqualWithNotEqualData($a, $b)
    {
        $this->assertFalse(Utils::areEqual($a, $b));
    }

    public function testAreEqualWithRecursiveReference()
    {
        $a = new \stdClass();
        $a->foo = [1, 2, 3];
        $a->bar = new \stdClass();
        $a->bar->baz = $a;

        $b = new \stdClass();
        $b->foo = [1, 2, 3];
        $b->bar = new \stdClass();
        $b->bar->baz = $b;

        $this->assertTrue(Utils::areEqual($a, $b));
    }

    public function equalDataProvider()
    {
        $a = new \stdClass();
        $b = new \stdClass();
        $a->foo = [1, 2, 3];
        $a->bar = new \stdClass();
        $b->foo = [1, 2, 3];
        $b->bar = new \stdClass();

        return [
            [1, 1],
            ['foo', 'foo'],
            [['foo', 'bar', []], ['foo', 'bar', []]],
            [new \stdClass(), new \stdClass()],
            [$a, $b]
        ];
    }

    public function notEqualDataProvider()
    {
        $a = new \stdClass();
        $b = new \stdClass();
        $a->foo = [1, 2, 3];
        $a->bar = new \stdClass();
        $a->bar->baz = [1];
        $b->foo = [1, 2, 3];
        $b->bar = new \stdClass();
        $b->bar->baz = [1, []];

        return [
            [1, -1],
            ['foo', 'bar'],
            [['foo', ['bar', 'baz']], ['foo', ['bar', 'quz']]],
            [$a, $b]
        ];
    }
}
