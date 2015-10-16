<?php

namespace JsonSchema\Exception;

class ResolverException extends \Exception
{
    const NO_BASE_SCHEMA                = 10;
    const UNRESOLVED_POINTER_PROPERTY   = 11;
    const INVALID_POINTER_INDEX         = 12;
    const UNRESOLVED_POINTER_INDEX      = 13;
    const INVALID_SEGMENT_TYPE          = 14;
    const INVALID_POINTER_TARGET        = 15;
    const SELF_REFERENCING_POINTER      = 16;
    const UNFETCHABLE_URI               = 17;
    const JSON_DECODE_ERROR             = 18;
    const INVALID_REMOTE_SCHEMA         = 19;
}
