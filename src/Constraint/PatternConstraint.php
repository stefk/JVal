<?php

namespace JVal\Constraint;

use JVal\Constraint;
use JVal\Context;
use JVal\Exception\Constraint\InvalidRegexException;
use JVal\Exception\Constraint\InvalidTypeException;
use JVal\Types;
use JVal\Walker;
use stdClass;

class PatternConstraint implements Constraint
{
    public function keywords()
    {
        return ['pattern'];
    }

    public function supports($type)
    {
        return $type === Types::TYPE_STRING;
    }

    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        $context->enterNode($schema->pattern, 'pattern');

        if (!is_string($schema->pattern)) {
            throw new InvalidTypeException($context, Types::TYPE_STRING);
        }

        if (@preg_match("/{$schema->pattern}/", '') === false) {
            throw new InvalidRegexException($context);
        }

        $context->leaveNode();
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        if (!preg_match("/{$schema->pattern}/", $instance)) {
            $context->addViolation('should match regex "%s"', [$schema->pattern]);
        }
    }
}
