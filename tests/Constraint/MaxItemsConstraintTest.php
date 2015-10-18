<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Testing\ConstraintTestCase;

class MaxItemsConstraintTest extends ConstraintTestCase
{
    public function testNormalize()
    {
        $this->markTestSkipped();
    }

    protected function getConstraint()
    {
        return new MaxItemsConstraint();
    }

    protected function getCaseFileNames()
    {
        return ['maxItems'];
    }
}
