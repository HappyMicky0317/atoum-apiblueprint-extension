<?php

declare(strict_types=1);

namespace atoum\apiblueprint;

use mageekguy\atoum\writers\file;

class Compiler
{
    protected static $_parser = null;
    protected static $_target = null;

    public function compile(\Traversable $finder, file $outputFile = null, Parser $parser = null, Target $target = null): string
    {
        if (null === $outputFile) {
            $outputDirectory = sys_get_temp_dir() . '/atoum/apiblueprint/' . uniqid();

            if (false === is_dir($outputDirectory)) {
                mkdir($outputDirectory, 0777, true);
            }

            $outputFileName = $outputDirectory . '/testSuite.php';

            if (true === file_exists($outputFileName)) {
                unlink($outputFileName);
            }

            $outputFile = new file($outputFileName);
        }

        $outputFile->write('<?php' . "\n\n" . 'declare(strict_types=1);' . "\n\n");

        $parser = $parser ?? static::getParser();
        $target = $target ?? static::getTarget();

        foreach ($finder as $splFileInfo) {
            $intermediateRepresentation = $parser->parse(
                file_get_contents($splFileInfo->getPathname())
            );
            $target->compile($intermediateRepresentation, $outputFile);
        }

        return $outputFile->getFilename();
    }

    public static function getParser(): Parser
    {
        if (null === static::$_parser) {
            static::$_parser = new Parser();
        }

        return static::$_parser;
    }

    public static function getTarget(): Target
    {
        if (null === static::$_target) {
            static::$_target = new Target();
        }

        return static::$_target;
    }
}
