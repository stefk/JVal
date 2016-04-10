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
     * @var array
     */
    private static $phpToJson = [
        'array' => self::TYPE_ARRAY,
        'boolean' => self::TYPE_BOOLEAN,
        'double' => self::TYPE_NUMBER,
        'integer' => self::TYPE_INTEGER,
        'NULL' => self::TYPE_NULL,
        'object' => self::TYPE_OBJECT,
        'string' => self::TYPE_STRING,
    ];

    /**
     * @var array
     */
    private static $jsonToPhp = [
        self::TYPE_ARRAY => 'array',
        self::TYPE_BOOLEAN => 'boolean',
        self::TYPE_INTEGER => 'integer',
        self::TYPE_NUMBER => 'double',
        self::TYPE_NULL => 'NULL',
        self::TYPE_OBJECT => 'object',
        self::TYPE_STRING => 'string',
    ];

    /**
     * Maps PHP native types to set of compatible JSON types.
     *
     * @var array
     */
    private static $typeCompatibility = [
        'array' => ['array' => true],
        'boolean' => ['boolean' => true],
        'double' => ['number' => true],
        'integer' => ['integer' => true, 'number' => true],
        'NULL' => ['null' => true],
        'object' => ['object' => true],
        'resource' => [],
        'string' => ['string' => true],
        'unknown type' => [],
    ];

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
        $phpType = gettype($instance);

        if (isset(self::$phpToJson[$phpType])) {
            return self::$phpToJson[$phpType];
        }

        throw new UnsupportedTypeException($phpType);
    }

    /**
     * Returns whether an instance matches a given JSON type.
     *
     * @param mixed  $instance
     * @param string $type
     *
     * @return bool
     */
    public static function isA($instance, $type)
    {
        return isset(self::$typeCompatibility[gettype($instance)][$type]);
    }

    /**
     * Returns whether an instance matches at least one of given JSON types.
     *
     * @param mixed    $instance
     * @param string[] $types
     *
     * @return bool
     */
    public static function isOneOf($instance, array $types)
    {
        $possible = self::$typeCompatibility[gettype($instance)];

        foreach ($types as $type) {
            if (isset($possible[$type])) {
                return true;
            }
        }

        return false;
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
        return isset(self::$jsonToPhp[$type]);
    }
}
