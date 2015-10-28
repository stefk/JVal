<?php

namespace JVal\Constraint;

use JVal\Constraint;
use JVal\Context;
use JVal\Exception\Constraint\InvalidTypeException;
use JVal\Types;
use JVal\Utils;
use JVal\Walker;
use stdClass;

class UniqueItemsConstraint implements Constraint
{
    public function keywords()
    {
        return ['uniqueItems'];
    }

    public function supports($type)
    {
        return $type === Types::TYPE_ARRAY;
    }

    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        if (!is_bool($schema->uniqueItems)) {
            $context->enterNode($schema->uniqueItems, 'uniqueItems');

            throw new InvalidTypeException($context, Types::TYPE_BOOLEAN);
        }
    }

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
