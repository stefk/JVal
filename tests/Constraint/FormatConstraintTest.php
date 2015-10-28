<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal\Constraint;

use JVal\Context;
use JVal\Testing\ConstraintTestCase;

class FormatConstraintTest extends ConstraintTestCase
{
    public function testNormalizeThrowsIfFormatIsNotString()
    {
        $this->expectConstraintException('InvalidTypeException', '/format');
        $schema = $this->loadSchema('invalid/format-not-string');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    protected function getConstraint()
    {
        return new FormatConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['format'];
    }
}
