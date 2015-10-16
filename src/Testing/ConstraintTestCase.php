<?php

namespace JsonSchema\Testing;

abstract class ConstraintTestCase extends BaseTestCase
{
    protected function setUp()
    {
        $this->setExceptionClass('JsonSchema\Exception\ConstraintException');
    }
}
