<?php

declare(strict_types=1);

namespace atoum\apiblueprint;

use Hoa\Ustring;
use atoum\apiblueprint\IntermediateRepresentation as IR;
use mageekguy\atoum\writers\file;

class Target
{
    public function compile(IR\Document $document, file $outputFile)
    {
        $outputFile->write('namespace atoum\apiblueprint\generated;' . "\n\n");

        $testSuiteName = $document->apiName;

        if (empty($testSuiteName)) {
            $testSuiteName = 'Unknown' . sha1(serialize($document));
        } else {
            $testSuiteName = $this->stringToPHPIdentifier($testSuiteName, false);
        }

        $outputFile->write(
            'class ' . $testSuiteName . ' extends \atoum\apiblueprint\test' . "\n" .
            '{' . "\n" .
            '    protected $_host = null;' . "\n\n" .
            '    public function beforeTestMethod($testMethod)' . "\n" .
            '    {' . "\n" .
            '        $this->_host = \'' . $document->metadata['host'] . '\';' . "\n" .
            '    }'
        );

        foreach ($document->resources as $i => $resource) {
            $resourceName = $resource->name;

            if (empty($resourceName)) {
                $resourceName = 'test resource unknonwn' . $i;
            } else {
                $resourceName = 'test resource ' . $this->stringToPHPIdentifier($resourceName);
            }

            $this->compileResource($resource, $resourceName, $outputFile);
        }

        $outputFile->write("\n" . '}');
    }

    public function compileResource(IR\Resource $resource, string $resourceName, file $outputFile)
    {
        if (empty($resource->actions)) {
            $outputFile->write(
                "\n\n" .
                '    public function ' . $resourceName . '()' . "\n" .
                '    {' . "\n" .
                '        $this->skip(\'No action for the resource `' . $resource->name . '`.\');' . "\n" .
                '    }'
            );

            return;
        }

        foreach ($resource->actions as $i => $action) {
            $actionName = $action->name;

            if (empty($actionName)) {
                $actionName = 'action unknown' . $i;
            } else {
                $actionName = 'action ' . $this->stringToPHPIdentifier($actionName);
            }

            $this->compileAction($action, $actionName, $resource, $resourceName, $outputFile);
        }
    }

    public function compileAction(IR\Action $action, string $actionName, IR\Resource $resource, string $resourceName, file $outputFile)
    {
        static $_defaultPayload = null;

        if (null === $_defaultPayload) {
            $_defaultPayload = new IR\Payload();
        }

        if (empty($action->messages)) {
            $outputFile->write(
                "\n\n" .
                '    public function ' . $resourceName . ' ' . $actionName . '()' . "\n" .
                '    {' . "\n" .
                '        $this->skip(\'Action `' .$action->name . '` for the resource `' . $resource->name . '` has no message.\');' . "\n" .
                '    }'
            );
        } else {
            foreach ($this->groupMessagesByTransactions($action) as $i => $transaction) {
                $outputFile->write(
                    "\n\n" .
                    '    public function ' . $resourceName . ' ' . $actionName . ' transaction ' . $i . '()' . "\n" .
                    '    {' . "\n" .
                    '        $requester = new \atoum\apiblueprint\Http\Requester();' . "\n" .
                    '        $expectedResponses = [];' . "\n\n"
                );

                foreach ($transaction as $message) {
                    if ($message instanceof IR\Request) {
                        $payload = $message->payload ?? $_defaultPayload;
                        $uri     = $action->uriTemplate;

                        if (empty($uri)) {
                            $uri = $resource->uriTemplate;
                        }

                        $outputFile->write(
                            '        $requester->addRequest(' . "\n" .
                            '            \'' . $action->requestMethod . '\',' . "\n" .
                            '            $this->_host . \'' . $uri . '\',' . "\n" .
                            '            [' . "\n" .
                            $this->arrayAsStringRepresentation($payload->headers, '                ') .
                            '            ]' . "\n" .
                            '        );' . "\n"
                        );
                    } elseif ($message instanceof IR\Response) {
                        $payload = $message->payload ?? $_defaultPayload;

                        $outputFile->write(
                            '        $expectedResponses[] = [' . "\n" .
                            '            \'statusCode\' => ' . $message->statusCode . ',' . "\n" .
                            '            \'mediaType\'  => \'' . $message->mediaType . '\',' . "\n" .
                            '            \'headers\'    => [' . "\n" .
                            $this->arrayAsStringRepresentation($payload->headers, '                ') .
                            '            ],' . "\n" .
                            '            \'body\'       => ' . var_export(trim($payload->body), true) . ',' . "\n" .
                            '            \'schema\'     => ' . var_export($payload->schema, true) . ',' . "\n" .
                            '        ];' . "\n"
                        );
                    }
                }

                $outputFile->write(
                    "\n" .
                    '        $this->responsesMatch($requester->send(), $expectedResponses);' . "\n" .
                    '    }'
                );
            }
        }
    }

    protected function arrayAsStringRepresentation(array $array, string $linePrefix = ''): string
    {
        $out = '';

        foreach ($array as $key => $value) {
            $out .= $linePrefix . var_export($key, true) . ' => ' . var_export($value, true) . ',' . "\n";
        }

        return $out;
    }

    public function groupMessagesByTransactions(IR\Action $action): \Generator
    {
        $transaction           = $action->messages;
        $transactionHasRequest = false;

        foreach ($transaction as $message) {
            if ($message instanceof IR\Request) {
                $transactionHasRequest = true;

                break;
            }
        }

        if (false === $transactionHasRequest) {
            array_unshift($transaction, new IR\Request());
        }

        yield $transaction;
    }

    public function stringToPHPIdentifier(string $string, bool $toLowerCase = true): string
    {
        $identifier = (new UString($string))->toAscii()->replace('/[^a-zA-Z0-9_\x80-\xff]/', ' ');

        if (true === $toLowerCase) {
            $identifier->toLowerCase();
        }

        return trim((string) $identifier, ' ');
    }
}
