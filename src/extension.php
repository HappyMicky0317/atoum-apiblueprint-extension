<?php

declare(strict_types=1);

namespace atoum\apiblueprint;

use mageekguy\atoum;

class extension implements atoum\extension
{
    protected $_configuration   = null;
    protected $_apibFinder      = null;
    protected $_directoryToTest = null;

    public function __construct(atoum\configurator $configurator = null)
    {
        if ($configurator) {
            $parser = $configurator->getScript()->getArgumentsParser();

            $selfHandler = function ($script, $argument, $values) {
                $runner = $script->getRunner();

                $runner->addTestsFromDirectory(dirname(__DIR__) . '/test/integration/');
                $runner->addTestsFromDirectory(dirname(__DIR__) . '/test/unit/');
            };

            $directoryToTest = &$this->_directoryToTest;

            $extensionHandler = function ($script, $argument, $values) use (&$directoryToTest) {
                if (null !== $directoryToTest) {
                    $script->getRunner()->addTestsFromDirectory($directoryToTest);
                }
            };

            $parser
                ->addHandler($selfHandler, ['--test-ext'])
                ->addHandler($selfHandler, ['--test-it'])
                ->addHandler($extensionHandler, ['--extension-apiblueprint']);
        }

        $this->_configuration = new Configuration();
        $this->_apibFinder    = new Finder();
    }

    public function addToRunner(atoum\runner $runner)
    {
        $runner->addExtension($this, $this->getConfiguration());

        // This trick is necessary to add the directory containing the
        // generated tests after `atoum\runner::resetTestPaths` has been
        // called.
        $_SERVER['argv'][] = '--extension-apiblueprint';

        return $this;
    }

    public function setRunner(atoum\runner $runner)
    {
        return $this;
    }

    public function setTest(atoum\test $test)
    {
        if ($test instanceof test) {
            $test->setJsonHandler($test->getExtensionConfiguration($this));
        }

        return $this;
    }

    public function handleEvent($event, atoum\observable $observable)
    {
    }

    public function getConfiguration(): Configuration
    {
        return $this->_configuration;
    }

    /**
     * Return the real finder instance.
     */
    public function getRawAPIBFinder(): Finder
    {
        return $this->_apibFinder;
    }

    /**
     * Return the inner iterator of the iterator, which is a
     * `AppendIterator`. The user can simply add file system iterators to the
     * finder.
     */
    public function getAPIBFinder(): \AppendIterator
    {
        return $this->_apibFinder->getInnerIterator();
    }

    public function compileAndEnqueue()
    {
        $this->_directoryToTest = dirname((new Compiler())->compile($this->_apibFinder));
    }
}
