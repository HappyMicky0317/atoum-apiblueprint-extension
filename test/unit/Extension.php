<?php

declare(strict_types=1);

namespace atoum\apiblueprint\test\unit;

use atoum\apiblueprint as LUT;
use atoum\apiblueprint\extension as SUT;
use mageekguy\atoum\test;

class Extension extends test
{
    public function test_add_to_runner()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $runner = new \mock\mageekguy\atoum\runner(),
                $this->calling($runner)->addExtension->doesNothing(),

                $extension = new SUT()
            )
            ->when($result = $extension->addToRunner($runner))
            ->then
                ->object($result)
                    ->isIdenticalTo($extension)
                ->mock($runner)
                    ->call('addExtension')->withIdenticalArguments($extension)->once();
    }

    public function test_get_apib_finder()
    {
        $this
            ->given($extension = new SUT())
            ->when($result = $extension->getAPIBFinder())
            ->then
                ->object($result)
                    ->isInstanceOf(\AppendIterator::class)
                ->object($extension->getRawAPIBFinder()->getInnerIterator())
                    ->isIdenticalTo($result);
    }
}
