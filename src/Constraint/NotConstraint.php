<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\Constraint\InvalidTypeException;
use JsonSchema\Types;
use JsonSchema\Walker;
use stdClass;

class NotConstraint implements Constraint
{
    public function keywords()
    {
        return ['not'];
    }

    public function supports($type)
    {
        return true;
    }

    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        $context->enterNode($schema->not, 'not');

        if (!is_object($schema->not)) {
            throw new InvalidTypeException($context, Types::TYPE_OBJECT);
        }

        $walker->parseSchema($schema->not, $context);
        $context->leaveNode();
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        $altContext = $context->duplicate();
        $walker->applyConstraints($instance, $schema->not, $altContext);

        if ($altContext->countViolations() === $context->countViolations()) {
            $context->addViolation('should not match schema in "not"');
        }
    }
}
