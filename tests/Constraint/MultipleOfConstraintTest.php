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

class MultipleOfConstraintTest extends ConstraintTestCase
{
    public function testNormalizeThrowsIfNotANumber()
    {
        $this->expectConstraintException('InvalidTypeException', '/multipleOf');
        $schema = $this->loadSchema('invalid/multiple-of-not-number');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    /**
     * @dataProvider nonPositiveMultipleOfProvider
     *
     * @param string $schemaName
     */
    public function testNormalizeThrowsOnNonPositiveNumber($schemaName)
    {
        $this->expectConstraintException('NotStrictlyPositiveException', '/multipleOf');
        $schema = $this->loadSchema($schemaName);
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function nonPositiveMultipleOfProvider()
    {
        return [
            ['invalid/multiple-of-not-positive-1'],
            ['invalid/multiple-of-not-positive-2'],
        ];
    }

    protected function getConstraint()
    {
        return new MultipleOfConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['multipleOf'];
    }
}
