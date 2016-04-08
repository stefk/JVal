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
     * @var Constraint[][]
     */
    private $constraintsForTypeCache;

    /**
     * Returns the constraints associated with a given JSON Schema version.
     *
     * @param string $version
     *
     * @return Constraint[]
     *
     * @throws UnsupportedVersionException if the version is not supported
     */
    public function getConstraints($version)
    {
        if (!isset($this->constraints[$version])) {
            $this->constraints[$version] = $this->createConstraints($version);
        }

        return $this->constraints[$version];
    }

    /**
     * Returns the constraints associated with a given JSON Schema version
     * which supports given JSON type.
     *
     * @param string $version
     * @param string $type
     *
     * @return Constraint[]
     *
     * @throws UnsupportedVersionException if the version is not supported
     */
    public function getConstraintsForType($version, $type)
    {
        $cache = & $this->constraintsForTypeCache[$version.$type];

        if ($cache === null) {
            $cache = [];

            foreach ($this->getConstraints($version) as $constraint) {
                if ($constraint->supports($type)) {
                    $cache[] = $constraint;
                }
            }
        }

        return $cache;
    }

    /**
     * Loads the constraints associated with a given JSON Schema version.
     *
     * @param string $version
     *
     * @return Constraint[]
     *
     * @throws \Exception if the version is not supported
     */
    protected function createConstraints($version)
    {
        switch ($version) {
            case self::VERSION_CURRENT:
            case self::VERSION_DRAFT_4:
                return $this->createBuiltInConstraints(
                    array_merge(
                        self::$commonConstraints,
                        self::$draft4Constraints
                    )
                );
            default:
                throw new UnsupportedVersionException(
                    "Schema version '{$version}' not supported"
                );
        }
    }

    private function createBuiltInConstraints(array $constraintNames)
    {
        return array_map(function ($name) {
            $class = "JVal\\Constraint\\{$name}Constraint";

            return new $class();
        }, $constraintNames);
    }
}
