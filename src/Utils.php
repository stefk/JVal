<?php

namespace JVal;

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
        return self::doAreEqual($a, $b, []);
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
