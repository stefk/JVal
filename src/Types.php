<?php

namespace JsonSchema;

use JsonSchema\Exception\UnsupportedTypeException;

class Types
{
    const TYPE_ARRAY    = 'array';
    const TYPE_BOOLEAN  = 'boolean';
    const TYPE_INTEGER  = 'integer';
    const TYPE_NUMBER   = 'number';
    const TYPE_NULL     = 'null';
    const TYPE_OBJECT   = 'object';
    const TYPE_STRING   = 'string';

    /**
     * Returns the type of an instance according to JSON Schema Core 3.5.
     *
     * @param mixed $instance
     * @return string
     * @throws TypeException
     */
    public static function getPrimitiveTypeOf($instance)
    {
        switch ($type = gettype($instance)) {
            case 'array':
                return self::TYPE_ARRAY;
            case 'boolean':
                return self::TYPE_BOOLEAN;
            case 'integer':
                return self::TYPE_INTEGER;
            case 'NULL':
                return self::TYPE_NULL;
            case 'double':
                return self::TYPE_NUMBER;
            case 'object':
                return self::TYPE_OBJECT;
            case 'string':
                return self::TYPE_STRING;
        }

        throw new UnsupportedTypeException($type);
    }

    public static function isA($instance, $type)
    {
        $actualType = self::getPrimitiveTypeOf($instance);

        return $actualType === $type
            || $actualType === self::TYPE_INTEGER && $type === self::TYPE_NUMBER;
    }
}
