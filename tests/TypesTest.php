<?php

namespace JsonSchema;

use JsonSchema\Testing\BaseTestCase;

class TypesTest extends BaseTestCase
{
    /**
     * @dataProvider instanceTypeProvider
     *
     * @param mixed     $instance
     * @param string    $expectedType
     */
    public function testGetPrimitiveType($instance, $expectedType)
    {
        $actualType = Types::getPrimitiveTypeOf($instance);
        $this->assertEquals($expectedType, $actualType);
    }

    /**
     * @expectedException \JsonSchema\Exception\UnsupportedTypeException
     */
    public function testGetPrimitiveTypeThrowsOnUnsupportedType()
    {
        Types::getPrimitiveTypeOf(fopen(__FILE__, 'r'));
    }

    public function testIsA()
    {
        $this->assertTrue(Types::isA([], Types::TYPE_ARRAY));
        $this->assertTrue(Types::isA('foo', Types::TYPE_STRING));
        $this->assertTrue(Types::isA(123, Types::TYPE_INTEGER));
        $this->assertTrue(Types::isA(123, Types::TYPE_NUMBER));
        $this->assertFalse(Types::isA(1.23, Types::TYPE_INTEGER));
        $this->assertTrue(Types::isA(1.23, Types::TYPE_NUMBER));
    }

    public function instanceTypeProvider()
    {
        return [
            [[1, 2, 3], Types::TYPE_ARRAY],
            [true, Types::TYPE_BOOLEAN],
            [123, Types::TYPE_INTEGER],
            [1.23, Types::TYPE_NUMBER],
            [null, Types::TYPE_NULL],
            [new \stdClass(), Types::TYPE_OBJECT],
            ['123', Types::TYPE_STRING],
        ];
    }
}
