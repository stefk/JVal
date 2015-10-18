<?php

namespace JsonSchema\Exception;

class ConstraintException extends \Exception
{
    const MAXIMUM_NOT_PRESENT           = 10;
    const MAXIMUM_NOT_NUMBER            = 11;
    const EXCLUSIVE_MAXIMUM_NOT_BOOLEAN = 12;
    const MULTIPLE_OF_NOT_POSITIVE      = 13;
    const MULTIPLE_OF_NOT_NUMBER        = 14;
    const ITEMS_INVALID_TYPE            = 15;
    const ITEMS_ELEMENT_NOT_OBJECT      = 16;
    const ADDITIONAL_ITEMS_INVALID_TYPE = 17;
}
