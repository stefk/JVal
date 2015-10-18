<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Testing\ConstraintTestCase;

class ItemsConstraintTest extends ConstraintTestCase
{
    public function testNormalize()
    {
        $this->markTestSkipped();
    }

    protected function getConstraint()
    {
        return new ItemsConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['items'];
    }
}
