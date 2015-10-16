<?php

namespace JsonSchema;

use JsonSchema\Constraint\ItemsConstraint;
use JsonSchema\Constraint\MaximumConstraint;
use JsonSchema\Constraint\MaxItemsConstraint;
use JsonSchema\Constraint\MaxPropertiesConstraint;
use JsonSchema\Constraint\MultipleOfConstraint;
use JsonSchema\Exception\RegistryException;

class Registry
{
    const VERSION_CURRENT = 'http://json-schema.org/schema#';
    const VERSION_DRAFT_3 = 'http://json-schema.org/draft-03/schema#';
    const VERSION_DRAFT_4 = 'http://json-schema.org/draft-04/schema#';

    const TYPE_ARRAY    = 'array';
    const TYPE_BOOLEAN  = 'boolean';
    const TYPE_INTEGER  = 'integer';
    const TYPE_NUMBER   = 'number';
    const TYPE_NULL     = 'null';
    const TYPE_OBJECT   = 'object';
    const TYPE_STRING   = 'string';

    private $constraints = [];
    private $loadedVersion;

    public function loadConstraintsFor($version)
    {
        if (!isset($this->constraints[$version])) {
            switch ($version) {
                case self::VERSION_CURRENT:
                case self::VERSION_DRAFT_4:
                    $this->constraints[$version] = array_merge(
                        $this->createCommonConstraints(),
                        $this->createDraft4Constraints()
                    );
                    break;
                default:
                    throw new \Exception("Schema version '{$version}' not supported");
            }
        }

        $this->loadedVersion = $version;
    }

    /**
     * @return Constraint[]
     * @throws \Exception
     */
    public function getConstraints()
    {
        if (!isset($this->loadedVersion)) {
            throw new \Exception('No constraints have been loaded yet');
        }

        return $this->constraints[$this->loadedVersion];
    }

    /**
     * Returns the type of an instance according to JSON Schema Core 3.5.
     *
     * @param mixed $instance
     * @return string
     * @throws RegistryException
     */
    public function getPrimitiveTypeOf($instance)
    {
        switch ($type = gettype($instance)) {
            case 'array':
                return self::TYPE_ARRAY;
            case 'boolean':
                return self::TYPE_BOOLEAN;
            case 'integer':
                return self::TYPE_INTEGER;
            case 'NULL':
                return self::TYPE_NULL;
            case 'double':
                return self::TYPE_NUMBER;
            case 'object':
                return self::TYPE_OBJECT;
            case 'string':
                return self::TYPE_STRING;
        }

        throw new RegistryException(
            "Unsupported type '{$type}'",
            RegistryException::UNSUPPORTED_TYPE
        );
    }

    private function createCommonConstraints()
    {
        return [
            new MaximumConstraint(),
            new MaxItemsConstraint(),
            new ItemsConstraint(),
            new MaxPropertiesConstraint()
        ];
    }

    private function createDraft4Constraints()
    {
        return [
            new MultipleOfConstraint()
        ];
    }
}
