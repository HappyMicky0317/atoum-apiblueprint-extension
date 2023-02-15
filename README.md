<p align="center">
  <img src="./res/logo.png" alt="atoum's extension logo" width="200" />
</p>

# atoum/apiblueprint-extension [![Build Status](https://travis-ci.org/Hywan/atoum-apiblueprint-extension.svg?branch=master)](https://travis-ci.org/Hywan/atoum-apiblueprint-extension)

**The candidates**:

  1. [atoum](http://atoum.org/) is a PHP test framework,
  2. [API Blueprint](https://apiblueprint.org/) is a high-level HTTP
      API description language.

**The problem**: API Blueprint is only a text file. Easy to read for
human, but a machine can't do anything with it.

**The solution**: Compile API Blueprint files into executable tests. It
works as any test written with the atoum API, and it works within the
atoum ecosystem. Here is an overview of the workflow:

<p align="center">
  <img src="./res/overview.svg" alt="Process overview" width="580" />
</p>

In more details, here is what happens:

  1. A finder iterates over `.apib` files,
  2. For each file, it is parsed into an intermediate representation,
  3. The intermediate representation is compiled to target “atoum
     tests”,
  4. The fresh tests are pushed in the test queue of atoum,
  5. atoum executes everything as usual.

**The bonus**: A very simple script is provided to _render_ many API
Blueprint files as a standalone HTML single-page file.

## Installation and configuration

With [Composer](https://getcomposer.org/), to include this extension into
your dependencies, you need to
require
[`atoum/apiblueprint-extension`](https://packagist.org/packages/atoum/apiblueprint-extension):

```sh
$ composer require atoum/apiblueprint-extension '~0.2'
```

To enable the extension, the `.atoum.php` configuration file must be edited to add:

```php
$extension = new atoum\apiblueprint\extension($script);
$extension->addToRunner($runner);

```

### Configure the finder

Assuming the `.apib` files are located in the `./apiblueprints`
directory, the following code adds this directory to the API Blueprint
finder, compiles everything to tests, and enqueue them:

```php
$extension->getAPIBFinder()->append(new FilesystemIterator('./apiblueprints'));
$extension->compileAndEnqueue();
```

### Configure the location of JSON schemas when defined outside `.apib` files

API Blueprint uses [JSON Schema](http://json-schema.org/)
to
[validate HTTP requests and responses](https://apiblueprint.org/documentation/advanced-tutorial.html#json-schema) when
the message aims at being a valid JSON message.

We recommend to define JSON schemas outside the `.apib` files for several reasons:

  * They can be versionned independently from the `.apib` files,
  * They can be used inside your application to validate incoming HTTP
    requests or outgoing HTTP responses,
  * They can be used by other tools.

To do so, one must go through these 2 steps:

  1. Mount a JSON schema directory with the help of the extension's
     configuration,
  2. Use `{"$ref": "json-schema://<mount>/schema.json"}` in the [Schema
     section](https://apiblueprint.org/documentation/advanced-tutorial.html#json-schema).
     
Example:

  1. In the `.atoum.php` file where the extension is configured:

     ```php
     $extension->getConfiguration()->mountJsonSchemaDirectory('test', '/path/to/schemas/');
     ```

  2. In the API Blueprint file:
  
     ```apib
     + Response 200

       + Schema

         {"$ref": "json-schema://test/api-foo/my-schema.json"}
     ```

     where `test` is the “mount point name”, and
     `/api-foo/my-schema.json` is a valid JSON schema file located at
     `/path/to/schemas/api-foo/my-schema.json`.

## Testing

Before running the test suites, the development dependencies must be installed:

```sh
$ composer install
```

Then, to run all the test suites:

```sh
$ composer test
```

## Compliance with the API Blueprint specification

This atoum extension implements the
[API Blueprint specification](https://apiblueprint.org/documentation/specification.html).

| Language features           | Implemented? |
|-----------------------------|--------------|
| Metadata section            | yes          |
| API name & overview section | yes          |
| Resource group section      | yes          |
| Resource section            | yes          |
| Resource model section      | no (ignored) |
| Schema section              | yes          |
| Action section              | yes          |
| Request section             | yes          |
| Response section            | yes          |
| URI parameters section      | no (ignored) |
| Attributes section          | no (ignored) |
| Headers section             | yes          |
| Body section                | yes          |
| Data Structures section     | no (ignored) |
| Relation section            | no (ignored) |

[Any help is welcomed](https://github.com/Hywan/atoum-apiblueprint-extension/issues/new)!

# License

Please, see the `LICENSE` file. This project uses the same license than atoum.
