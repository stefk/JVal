<?php

namespace JsonSchema;

class Utils
{
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
                if (!self::areEqual($value, $b[$key])) {
                    return false;
                }
            }
        } else {
            return $a === $b;
        }

        return true;
    }
}
