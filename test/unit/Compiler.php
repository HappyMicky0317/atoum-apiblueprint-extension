<?php

declare(strict_types=1);

namespace atoum\apiblueprint\test\unit;

use atoum\apiblueprint\Compiler as SUT;
use atoum\apiblueprint as LUT;
use mageekguy\atoum\test;

class Compiler extends test
{
    public function test_compile_with_no_output_file()
    {
        $self = $this;

        $this
            ->given(
                $compiler = new SUT(),
                $this->function->file_exists = function ($path) use ($self): bool {
                    $self
                        ->string($path)
                            ->matches('#^' . sys_get_temp_dir() . '/atoum/apiblueprint/([^\.]+)/testSuite\.php$#');

                    return false;
                }
            )
            ->when($result = $compiler->compile(new LUT\Finder()))
            ->then
                ->function('file_exists')->once();
    }

    public function test_compile_to_an_empty_target()
    {
        $self = $this;

        $this
            ->given(
                $finder = new LUT\Finder(),
                $finder->getInnerIterator()->append(new \FilesystemIterator(dirname(__DIR__) . '/fixtures/finder/z')),

                $this->mockGenerator->orphanize('__construct'),
                $outputFile = new \mock\mageekguy\atoum\writers\file('php://memory'),
                $this->calling($outputFile)->write->doesNothing(),
                $this->calling($outputFile)->getFilename = 'php://memory',

                $parser                        = new \mock\atoum\apiblueprint\Parser(),
                $document                      = new LUT\IntermediateRepresentation\Document(),
                $this->calling($parser)->parse = $document,

                $target = new \mock\atoum\apiblueprint\Target(),
                $this->calling($target)->compile->doesNothing(),

                $compiler = new SUT()
            )
            ->when($result = $compiler->compile($finder, $outputFile, $parser, $target))
            ->then
                ->string($result)
                    ->isEqualTo('php://memory')
                ->mock($parser)
                    ->call('parse')->withIdenticalArguments('baz')->once()
                ->mock($target)
                    ->call('compile')->withIdenticalArguments($document, $outputFile)->once();
    }

    public function test_get_parser()
    {
        $this
            ->when($result = SUT::getParser())
            ->then
                ->object($result)
                    ->isInstanceOf(LUT\Parser::class)
                    ->isIdenticalTo(SUT::getParser());
    }

    public function test_get_target()
    {
        $this
            ->when($result = SUT::getTarget())
            ->then
                ->object($result)
                    ->isInstanceOf(LUT\Target::class)
                    ->isIdenticalTo(SUT::getTarget());
    }
}
