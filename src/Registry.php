<?php

namespace JsonSchema;

use JsonSchema\Constraint;

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
            new Constraint\MaximumConstraint(),
            new Constraint\MinimumConstraint(),
            new Constraint\MaxLengthConstraint(),
            new Constraint\MinLengthConstraint(),
            new Constraint\PatternConstraint(),
            new Constraint\ItemsConstraint(),
            new Constraint\MaxItemsConstraint(),
            new Constraint\MinItemsConstraint(),
            new Constraint\UniqueItemsConstraint(),
            new Constraint\MaxPropertiesConstraint(),
            new Constraint\MinPropertiesConstraint(),
            new Constraint\RequiredConstraint(),
            new Constraint\PropertiesConstraint(),
            new Constraint\DependenciesConstraint(),
            new Constraint\EnumConstraint(),
            new Constraint\TypeConstraint(),
            new Constraint\FormatConstraint()
        ];
    }

    private function createDraft4Constraints()
    {
        return [
            new Constraint\MultipleOfConstraint(),
            new Constraint\MaxPropertiesConstraint(),
            new Constraint\AllOfConstraint(),
            new Constraint\AnyOfConstraint(),
            new Constraint\OneOfConstraint(),
            new Constraint\NotConstraint(),
            new Constraint\MinPropertiesConstraint(),
            new Constraint\MaxPropertiesConstraint()
        ];
    }
}
