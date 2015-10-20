<?php

namespace JsonSchema;

use JsonSchema\Constraint\AllOfConstraint;
use JsonSchema\Constraint\AnyOfConstraint;
use JsonSchema\Constraint\ItemsConstraint;
use JsonSchema\Constraint\MaximumConstraint;
use JsonSchema\Constraint\MaxItemsConstraint;
use JsonSchema\Constraint\MaxPropertiesConstraint;
use JsonSchema\Constraint\MultipleOfConstraint;
use JsonSchema\Constraint\OneOfConstraint;
use JsonSchema\Constraint\PropertiesConstraint;
use JsonSchema\Constraint\TypeConstraint;
use JsonSchema\Constraint\UniqueItemsConstraint;

class Registry
{
    const VERSION_CURRENT = 'http://json-schema.org/schema#';
    const VERSION_DRAFT_3 = 'http://json-schema.org/draft-03/schema#';
    const VERSION_DRAFT_4 = 'http://json-schema.org/draft-04/schema#';

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

    private function createCommonConstraints()
    {
        return [
            new MaximumConstraint(),
            new MaxItemsConstraint(),
            new ItemsConstraint(),
            new PropertiesConstraint(),
            new TypeConstraint()
        ];
    }

    private function createDraft4Constraints()
    {
        return [
            new MultipleOfConstraint(),
            new MaxPropertiesConstraint(),
            new AllOfConstraint(),
            new AnyOfConstraint(),
            new OneOfConstraint()
        ];
    }
}
