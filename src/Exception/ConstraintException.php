<?php

namespace JsonSchema\Exception;

class ConstraintException extends \Exception
{
    const MULTIPLE_OF_NOT_POSITIVE = 10;
    const MULTIPLE_OF_NOT_A_NUMBER = 11;
}
