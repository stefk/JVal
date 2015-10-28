<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal\Exception\Constraint;

use JVal\Exception\ConstraintException;

class NotUniqueException extends ConstraintException
{
    protected function buildMessage()
    {
        $this->message = sprintf('%s elements must be unique', $this->getTargetNode());
    }
}
