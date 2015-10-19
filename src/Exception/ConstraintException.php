<?php

namespace JsonSchema\Exception;

use JsonSchema\Context;

class ConstraintException extends \Exception
{
    const MAXIMUM_NOT_PRESENT                   = 10;
    const MAXIMUM_NOT_NUMBER                    = 11;
    const EXCLUSIVE_MAXIMUM_NOT_BOOLEAN         = 12;
    const MULTIPLE_OF_NOT_POSITIVE              = 13;
    const MULTIPLE_OF_NOT_NUMBER                = 14;
    const ITEMS_INVALID_TYPE                    = 15;
    const ITEMS_ELEMENT_NOT_OBJECT              = 16;
    const ADDITIONAL_ITEMS_INVALID_TYPE         = 17;
    const MAX_ITEMS_NOT_INTEGER                 = 18;
    const MAX_ITEMS_NOT_POSITIVE                = 19;
    const PROPERTIES_NOT_OBJECT                 = 20;
    const PROPERTY_VALUE_NOT_OBJECT             = 21;
    const ADDITIONAL_PROPERTIES_INVALID_TYPE    = 22;
    const PATTERN_PROPERTIES_NOT_OBJECT         = 23;
    const PATTERN_PROPERTIES_INVALID_REGEX      = 24;
    const PATTERN_PROPERTY_NOT_OBJECT           = 25;
    const MAX_PROPERTIES_NOT_INTEGER            = 26;
    const MAX_PROPERTIES_NOT_POSITIVE           = 27;

    private $context;

    public function __construct($message, $code, Context $context)
    {
        $message = sprintf(
            '%s (path: %s)',
            $message,
            $context->getCurrentPath() === '' ? '/' : $context->getCurrentPath()
        );
        $this->context = $context;
        parent::__construct($message, $code);
    }

    public function getContext()
    {
        return $this->context;
    }
}
