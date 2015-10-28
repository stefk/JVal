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
use JVal\Exception\Constraint\InvalidTypeException;
use JVal\Types;
use JVal\Walker;
use stdClass;

/**
 * Constraint for the "not" keyword.
 */
class NotConstraint implements Constraint
{
    /**
     * {@inheritDoc}
     */
    public function keywords()
    {
        return ['not'];
    }

    /**
     * {@inheritDoc}
     */
    public function supports($type)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        $context->enterNode($schema->not, 'not');

        if (!is_object($schema->not)) {
            throw new InvalidTypeException($context, Types::TYPE_OBJECT);
        }

        $walker->parseSchema($schema->not, $context);
        $context->leaveNode();
    }

    /**
     * {@inheritDoc}
     */
    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        $altContext = $context->duplicate();
        $walker->applyConstraints($instance, $schema->not, $altContext);

        if ($altContext->countViolations() === $context->countViolations()) {
            $context->addViolation('should not match schema in "not"');
        }
    }
}
