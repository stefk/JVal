<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal\Constraint;

use JVal\Context;
use JVal\Walker;
use stdClass;

/**
 * Constraint for the "oneOf" keyword.
 */
class OneOfConstraint extends AbstractOfConstraint
{
    /**
     * {@inheritdoc}
     */
    public function keywords()
    {
        return ['oneOf'];
    }

    /**
     * {@inheritdoc}
     */
    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        $originalCount = $context->countViolations();
        $hasMatch = false;
        $hasDoubleMatch = false;

        foreach ($schema->oneOf as $subSchema) {
            $subContext = $context->duplicate();
            $walker->applyConstraints($instance, $subSchema, $subContext);

            if ($subContext->countViolations() === $originalCount) {
                if (!$hasMatch) {
                    $hasMatch = true;
                } else {
                    $hasDoubleMatch = true;
                    break;
                }
            }
        }

        if (!$hasMatch || $hasDoubleMatch) {
            $context->addViolation('instance must match exactly one of the schemas listed in oneOf');
        }
    }
}
