<?php

declare(strict_types=1);

namespace atoum\apiblueprint\test\unit;

use atoum\apiblueprint\Finder as SUT;
use mageekguy\atoum\test;

class Finder extends test
{
    public function test_type()
    {
        $this
            ->when($result = new SUT())
            ->then
                ->object($result)
                    ->isInstanceOf(\CallbackFilterIterator::class);
    }

    public function test_empty()
    {
        $this
            ->given($finder = new SUT())
            ->when($result = iterator_to_array($finder))
            ->then
                ->array($result)
                    ->isEmpty();
    }

    public function test_find_only_apib_files()
    {
        $this
            ->given(
                $fixtures = dirname(__DIR__) . '/fixtures/finder',
                $finder = new SUT(),
                $finder->append(new \FilesystemIterator($fixtures . '/x')),
                $finder->append(new \FilesystemIterator($fixtures . '/y'))
            )
            ->when($result = iterator_to_array($finder))
            ->then
                ->array(
                    array_map(
                        function (\SplFileInfo $item): string {
                            return $item->getPathname();
                        },
                        $result
                    )
                )
                    ->hasSize(4)
                    ->containsValues([
                        $fixtures . '/x/b.apib',
                        $fixtures . '/x/c.apib',
                        $fixtures . '/y/x.apib',
                        $fixtures . '/y/y.apib'
                    ]);
    }
}
