<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal;

use JVal\Constraint;
use JVal\Exception\UnsupportedVersionException;

/**
 * Stores and exposes validation constraints.
 */
class Registry
{
    const VERSION_CURRENT = 'http://json-schema.org/schema#';
    const VERSION_DRAFT_3 = 'http://json-schema.org/draft-03/schema#';
    const VERSION_DRAFT_4 = 'http://json-schema.org/draft-04/schema#';

    /**
     * @var Constraint[]
     */
    private $constraints = [];

    /**
     * @var string
     */
    private $loadedVersion;

    /**
     * Loads the constraints associated with a given JSON Schema version.
     *
     * @param string $version
     * @throws \Exception if the version is not supported
     */
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
                    throw new UnsupportedVersionException(
                        "Schema version '{$version}' not supported"
                    );
            }
        }

        $this->loadedVersion = $version;
    }

    /**
     * Returns the loaded constraints.
     *
     * @return Constraint[]
     * @throws \LogicException if no constraints have been loaded
     */
    public function getConstraints()
    {
        if (!isset($this->loadedVersion)) {
            throw new \LogicException(
                'Cannot return constraints: no constraints have been loaded yet'
            );
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
            new Constraint\MinPropertiesConstraint(),
            new Constraint\MaxPropertiesConstraint(),
            new Constraint\AllOfConstraint(),
            new Constraint\AnyOfConstraint(),
            new Constraint\OneOfConstraint(),
            new Constraint\NotConstraint()
        ];
    }
}
