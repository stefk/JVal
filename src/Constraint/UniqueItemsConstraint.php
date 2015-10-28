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
use JVal\Utils;
use JVal\Walker;
use stdClass;

/**
 * Constraint for the "uniqueItems" keyword.
 */
class UniqueItemsConstraint implements Constraint
{
    /**
     * {@inheritDoc}
     */
    public function keywords()
    {
        return ['uniqueItems'];
    }

    /**
     * {@inheritDoc}
     */
    public function supports($type)
    {
        return $type === Types::TYPE_ARRAY;
    }

    /**
     * {@inheritDoc}
     */
    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        if (!is_bool($schema->uniqueItems)) {
            $context->enterNode($schema->uniqueItems, 'uniqueItems');

            throw new InvalidTypeException($context, Types::TYPE_BOOLEAN);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        if ($schema->uniqueItems === true) {
            foreach ($instance as $i => $aItem) {
                foreach ($instance as $j => $bItem) {
                    if ($i !== $j && Utils::areEqual($aItem, $bItem)) {
                        $context->addViolation('elements must be unique');
                        break 2;
                    }
                }
            }
        }
    }
}
