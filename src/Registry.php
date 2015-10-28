<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal;

use JVal\Exception\UnsupportedVersionException;

/**
 * Stores and exposes validation constraints per version.
 */
class Registry
{
    const VERSION_CURRENT = 'http://json-schema.org/schema#';
    const VERSION_DRAFT_3 = 'http://json-schema.org/draft-03/schema#';
    const VERSION_DRAFT_4 = 'http://json-schema.org/draft-04/schema#';

    private static $commonConstraints = [
        'Maximum',
        'Minimum',
        'MaxLength',
        'MinLength',
        'Pattern',
        'Items',
        'MaxItems',
        'MinItems',
        'UniqueItems',
        'Required',
        'Properties',
        'Dependencies',
        'Enum',
        'Type',
        'Format',
    ];

    private static $draft4Constraints = [
        'MultipleOf',
        'MinProperties',
        'MaxProperties',
        'AllOf',
        'AnyOf',
        'OneOf',
        'Not',
    ];

    /**
     * @var Constraint[][]
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
     *
     * @throws \Exception if the version is not supported
     */
    public function loadConstraintsFor($version)
    {
        if (!isset($this->constraints[$version])) {
            switch ($version) {
                case self::VERSION_CURRENT:
                case self::VERSION_DRAFT_4:
                    $this->constraints[$version] = $this->createConstraints(
                        array_merge(
                            self::$commonConstraints,
                            self::$draft4Constraints
                        )
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
     *
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

    private function createConstraints(array $constraintNames)
    {
        return array_map(function ($name) {
            $class = "JVal\\Constraint\\{$name}Constraint";

            return new $class();
        }, $constraintNames);
    }
}
