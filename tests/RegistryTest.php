<?php

namespace JsonSchema;

use JsonSchema\Exception\RegistryException;

class RegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider instanceTypeProvider
     * @param mixed     $instance
     * @param string    $expectedType
     */
    public function testGetPrimitiveType($instance, $expectedType)
    {
        $registry = new Registry();
        $actualType = $registry->getPrimitiveTypeOf($instance);
        $this->assertEquals($expectedType, $actualType);
    }

    public function testGetPrimitiveTypeThrowsOnUnsupportedType()
    {
        $this->setExpectedException(
            'JsonSchema\Exception\RegistryException',
            RegistryException::UNSUPPORTED_TYPE
        );

        $registry = new Registry();
        $registry->getPrimitiveTypeOf(fopen(__FILE__, 'r'));
    }

    public function instanceTypeProvider()
    {
        return [
            [[1, 2, 3], Registry::TYPE_ARRAY],
            [true, Registry::TYPE_BOOLEAN],
            [123, Registry::TYPE_INTEGER],
            [1.23, Registry::TYPE_NUMBER],
            [null, Registry::TYPE_NULL],
            [new \stdClass(), Registry::TYPE_OBJECT],
            ['123', Registry::TYPE_STRING],
        ];
    }
}
