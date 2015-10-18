<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Testing\ConstraintTestCase;

class MaxPropertiesConstraintTest extends ConstraintTestCase
{
    public function testNormalize()
    {
        $this->markTestSkipped();
    }

    protected function getConstraint()
    {
        return new MaxPropertiesConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['maxProperties'];
    }
}
