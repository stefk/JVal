<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal;

/**
 * Exposes common utility methods.
 */
class Utils
{
    private static $jsonErrors = [
        JSON_ERROR_NONE             => 'No errors',
        JSON_ERROR_DEPTH            => 'Maximum stack depth exceeded',
        JSON_ERROR_STATE_MISMATCH   => 'Underflow or the modes mismatch',
        JSON_ERROR_CTRL_CHAR        => 'Unexpected control character found',
        JSON_ERROR_SYNTAX           => 'Syntax error, malformed JSON',
        JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, possibly incorrectly encoded'
    ];

    /**
     * Returns whether two variables are equal. Always compares
     * values, not references, and walks each variable recursively
     * if needed.
     *
     * @param mixed $a
     * @param mixed $b
     * @return bool
     */
    public static function areEqual($a, $b)
    {
        return self::doAreEqual($a, $b, []);
    }

    /**
     * @codeCoverageIgnore
     *
     * Returns the error message resulting from the last call to
     * json_encode or json_decode functions.
     *
     * @return string
     */
    public static function lastJsonErrorMessage()
    {
        if (defined('json_last_error_msg')) {
            return json_last_error_msg();
        }

        $lastError = json_last_error();

        if (isset(static::$jsonErrors[$lastError])) {
            return static::$jsonErrors[$lastError];
        }

        return 'Unknown error';
    }

    /**
     * Returns whether a regex is valid. Regex is supposed to be
     * non-anchored (see JSON Schema Validation 3.3).
     *
     * @param string $regex
     * @return bool
     */
    public static function isValidRegex($regex)
    {
        $regex = str_replace('/', '\/', $regex);

        return @preg_match("/{$regex}/", '') !== false;
    }

    /**
     * Returns whether a string matches a regex. Regex is supposed to be
     * non-anchored (see JSON Schema Validation 3.3).
     *
     * @param string    $string
     * @param string    $regex
     * @return bool
     */
    public static function matchesRegex($string, $regex)
    {
        $regex = str_replace('/', '\/', $regex);

        return preg_match("/{$regex}/", $string) > 0;
    }

    public static function doAreEqual($a, $b, array $stack)
    {
        // keep track of object references to avoid infinite recursion
        if (is_object($a)) {
            if (in_array($a , $stack)) {
                return true;
            }

            $stack[] = $a;
        }

        if (gettype($a) !== gettype($b)) {
            return false;
        }

        if (is_object($a)) {
            $a = (array) $a;
            $b = (array) $b;
            ksort($a);
            ksort($b);
        }

        if (is_array($a)) {
            if (count($a) !== count($b)) {
                return false;
            }

            foreach ($a as $key => $value) {
                if (!array_key_exists($key, $b)) {
                    return false;
                }

                if (!self::doAreEqual($value, $b[$key], $stack)) {
                    return false;
                }
            }
        } else {
            return $a === $b;
        }

        return true;
    }
}
