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

class UniqueItemsConstraintTest extends ConstraintTestCase
{
    public function testNormalizeThrowsIfUniqueItemsIsNotBoolean()
    {
        $this->expectConstraintException('InvalidTypeException', '/uniqueItems');
        $schema = $this->loadSchema('invalid/uniqueItems-not-boolean');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    protected function getConstraint()
    {
        return new UniqueItemsConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['uniqueItems'];
    }
}
