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
 * Base class for constraints based on a numeric limit.
 */
abstract class AbstractRangeConstraint implements Constraint
{
    /**
     * {@inheritdoc}
     */
    public function supports($type)
    {
        return $type === Types::TYPE_INTEGER
            || $type === Types::TYPE_NUMBER;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        $property = property_exists($schema, $this->keywords()[0]) ?
            $this->keywords()[0] :
            $this->keywords()[1];

        if (!Types::isA($schema->{$property}, Types::TYPE_NUMBER)) {
            $context->enterNode($property);

            throw new InvalidTypeException($context, Types::TYPE_NUMBER);
        }
    }
}
