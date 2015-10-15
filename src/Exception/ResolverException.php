<?php

namespace JsonSchema\Exception;

class ResolverException extends \Exception
{
    const EMPTY_SCHEMA_STACK            = 10;
    const ALREADY_REGISTERED_URI        = 11;
    const UNRESOLVED_POINTER_PROPERTY   = 12;
    const INVALID_POINTER_INDEX         = 13;
    const UNRESOLVED_POINTER_INDEX      = 14;
    const INVALID_SEGMENT_TYPE          = 15;
    const INVALID_POINTER_TARGET        = 16;
    const SELF_REFERENCING_POINTER      = 17;
}
