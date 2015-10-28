<?php

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
