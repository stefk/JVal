<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal\Exception\Constraint;

use JVal\Context;
use JVal\Exception\ConstraintException;

class MissingKeywordException extends ConstraintException
{
    public function __construct(Context $context, $keyword)
    {
        parent::__construct($context, $keyword);
    }

    protected function buildMessage()
    {
        $this->message = sprintf('keyword %s must be present', $this->getTarget());
    }
}
