<?php

namespace JsonSchema\Constraint;

use JsonSchema\Context;
use JsonSchema\Testing\ConstraintTestCase;

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
