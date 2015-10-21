<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\Constraint\EmptyArrayException;
use JsonSchema\Exception\Constraint\InvalidTypeException;
use JsonSchema\Exception\Constraint\NotUniqueException;
use JsonSchema\Types;
use JsonSchema\Utils;
use JsonSchema\Walker;
use stdClass;

class EnumConstraint implements Constraint
{
    public function keywords()
    {
        return ['enum'];
    }

    public function supports($type)
    {
        return true;
    }

    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        $context->enterNode($schema->enum, 'enum');

        if (!is_array($schema->enum)) {
            throw new InvalidTypeException($context, Types::TYPE_ARRAY);
        }

        if (count($schema->enum) === 0) {
            throw new EmptyArrayException($context);
        }

        foreach ($schema->enum as $i => $aItem) {
            foreach ($schema->enum as $j => $bItem) {
                if ($i !== $j && Utils::areEqual($aItem, $bItem)) {
                    throw new NotUniqueException($context);
                }
            }
        }

        $context->leaveNode();
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        $hasMatch = false;

        foreach ($schema->enum as $value) {
            if (Utils::areEqual($instance, $value)) {
                $hasMatch = true;
                break;
            }
        }

        if (!$hasMatch) {
            $context->addViolation('should match one element in enum');
        }
    }
}
