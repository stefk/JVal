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

class PatternConstraintTest extends ConstraintTestCase
{
    public function testNormalizeThrowsIfPatternIsNotString()
    {
        $this->expectConstraintException('InvalidTypeException', '/pattern');
        $schema = $this->loadSchema('invalid/pattern-not-string');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testNormalizeThrowsIfPatternIsNotValidRegex()
    {
        $this->expectConstraintException('InvalidRegexException', '/pattern');
        $schema = $this->loadSchema('invalid/pattern-invalid-regex');
        $this->getConstraint()->normalize($schema, new Context(), $this->mockWalker());
    }

    public function testDelimitersAreEscapedBeforeTestingRegex()
    {
        $schema = $this->loadSchema('valid/pattern-with-slash');
        $context = new Context();
        $this->getConstraint()->normalize($schema, $context, $this->mockWalker());
        $this->assertEquals(0, $context->countViolations());
    }

    protected function getConstraint()
    {
        return new PatternConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['pattern'];
    }
}
