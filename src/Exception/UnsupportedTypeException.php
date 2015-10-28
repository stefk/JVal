<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal\Exception;

class UnsupportedTypeException extends \Exception
{
    public function __construct($type)
    {
        parent::__construct(sprintf('Unsupported type "%s"', $type));
    }
}
