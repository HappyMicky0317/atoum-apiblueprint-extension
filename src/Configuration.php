<?php

declare(strict_types=1);

namespace atoum\apiblueprint;

use mageekguy\atoum;
use RuntimeException;

class Configuration implements atoum\extension\configuration
{
    protected $_jsonSchemaMountPoints = [];

    /**
     * JSON schemas can be declared outside to the `.apib` files. To access
     * them, the user must use the `json-schema://host/path` URL
     * (e.g. `{"$ref": "json-schema://…"}`), where:
     *
     *   * `host` is the `$rootName`, and
     *   * The concatenation of `$directory` and `path` produces a valid path to a
     *     JSON schema file.
     */
    public function mountJsonSchemaDirectory(string $rootName, string $directory)
    {
        $_directory = realpath($directory);

        if (false === $_directory || false === is_dir($_directory)) {
            throw new RuntimeException(
                'Try to mount the `' . $directory . '` directory ' .
                'as `' . $rootName . '`, but it does not exist.'
            );
        }

        $this->_jsonSchemaMountPoints[$rootName] = $_directory;
    }

    /**
     * Remove a JSON schema directory that might have been mounted.
     */
    public function unmountJsonSchemaDirectory(string $rootName)
    {
        unset($this->_router[$rootName]);
    }

    /**
     * Returns all the JSON schema mount points.
     */
    public function getJsonSchemaMountPoints(): array
    {
        return $this->_jsonSchemaMountPoints;
    }

    /**
     * “Serialize” the configuration as an array.
     */
    public function serialize()
    {
        return [
            'jsonSchemaMountPoints' => $this->getJsonSchemaMountPoints()
        ];
    }

    /**
     * Allocate a `Configuration` object based on an array of data.
     */
    public static function unserialize(array $configuration)
    {
        $self = new static();

        if (isset($configuration['jsonSchemaMountPoints'])) {
            $self->_jsonSchemaMountPoints = $configuration['jsonSchemaMountPoints'];
        }

        return $self;
    }
}
