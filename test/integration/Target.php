<?php

declare(strict_types=1);

namespace atoum\apiblueprint\test\integration;

use atoum\apiblueprint as LUT;
use mageekguy\atoum\test;
use mageekguy\atoum\writers\file;

class Target extends test
{
    public function test_from_apib_files_to_atoum_tests()
    {
        $files  = new \FilesystemIterator(__DIR__ . '/ApibToTestSuites/');
        $parser = new LUT\Parser();
        $target = new LUT\Target();

        $resource  = (new \ReflectionClass(file::class))->getProperty('resource');
        $resource->setAccessible(true);

        $uri       = stream_get_meta_data(tmpfile())['uri'];
        $collector = new file($uri);

        $this
            ->executeOnFailure(
                function () use (&$file, $uri) {
                    echo
                        'Temporary file is `', $uri, '`.', "\n",
                        'Using file `', $file->getBasename(), '`.', "\n";
                }
            );

        foreach ($files as $file) {
            list($input, $output) = preg_split('/\s+---\[to\]---\s+/', file_get_contents($file->getPathname()));

            $target->compile($parser->parse($input), $collector);

            $this
                ->string(file_get_contents($uri))
                    ->isEqualTo($output);

            $collectorFileDescriptor = $resource->getValue($collector);
            ftruncate($collectorFileDescriptor, 0);
            rewind($collectorFileDescriptor);
        }

        unlink($uri);
    }
}
