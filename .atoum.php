<?php

$extension = new atoum\apiblueprint\extension($script);
$extension->addToRunner($runner);

$extension->getConfiguration()->mountJsonSchemaDirectory('test', __DIR__ . '/test/system/schemas/');
$extension->getAPIBFinder()->append(new FilesystemIterator(__DIR__ . '/test/system/'));

$extension->compileAndEnqueue();
