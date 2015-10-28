<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal\Constraint;

use JVal\Constraint;
use JVal\Context;
use JVal\Exception\Constraint\InvalidRegexException;
use JVal\Exception\Constraint\InvalidTypeException;
use JVal\Types;
use JVal\Walker;
use stdClass;

/**
 * Constraint for the "pattern" keyword.
 */
class PatternConstraint implements Constraint
{
    /**
     * {@inheritDoc}
     */
    public function keywords()
    {
        return ['pattern'];
    }

    /**
     * {@inheritDoc}
     */
    public function supports($type)
    {
        return $type === Types::TYPE_STRING;
    }

    /**
     * {@inheritDoc}
     */
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

    /**
     * {@inheritDoc}
     */
    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        if (!preg_match("/{$schema->pattern}/", $instance)) {
            $context->addViolation('should match regex "%s"', [$schema->pattern]);
        }
    }
}
