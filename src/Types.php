<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal;

use JVal\Exception\UnsupportedTypeException;

/**
 * Wraps the list of primitive types defined in the JSON Schema Core
 * specification and provides utility methods to deal with them.
 */
class Types
{
    const TYPE_ARRAY = 'array';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_INTEGER = 'integer';
    const TYPE_NUMBER = 'number';
    const TYPE_NULL = 'null';
    const TYPE_OBJECT = 'object';
    const TYPE_STRING = 'string';

    /**
     * Returns the type of an instance according to JSON Schema Core 3.5.
     *
     * @param mixed $instance
     *
     * @return string
     *
     * @throws UnsupportedTypeException
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

    /**
     * Returns whether an instance matches a given type.
     *
     * @param mixed  $instance
     * @param string $type
     *
     * @return bool
     */
    public static function isA($instance, $type)
    {
        $actualType = self::getPrimitiveTypeOf($instance);

        return $actualType === $type
            || $actualType === self::TYPE_INTEGER && $type === self::TYPE_NUMBER;
    }

    /**
     * Returns whether a type is part of the list of primitive types
     * defined by the specification.
     *
     * @param string $type
     *
     * @return bool
     */
    public static function isPrimitive($type)
    {
        return $type === self::TYPE_ARRAY
            || $type === self::TYPE_BOOLEAN
            || $type === self::TYPE_INTEGER
            || $type === self::TYPE_NUMBER
            || $type === self::TYPE_NULL
            || $type === self::TYPE_OBJECT
            || $type === self::TYPE_STRING;
    }
}
