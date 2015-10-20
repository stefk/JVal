<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Testing\ConstraintTestCase;

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

    protected function getConstraint()
    {
        return new PatternConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['pattern'];
    }
}
