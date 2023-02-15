<?php

declare(strict_types=1);

namespace atoum\apiblueprint;

use mageekguy\atoum;

class test extends atoum\test
{
    public function getTestedClassName()
    {
        return 'StdClass';
    }

    public function getTestedClassNamespace()
    {
        return '\\';
    }

    /**
     * Configure the `$this->json(…)` asserter.
     */
    public function setJsonHandler(Configuration $configuration)
    {
        $jsonSchemaUriRetriever = new JsonSchema\UriRetriever();

        foreach ($configuration->getJsonSchemaMountPoints() as $mountName => $mountValue) {
            $jsonSchemaUriRetriever->mount($mountName, $mountValue);
        }

        $self         = $this;
        $jsonAsserter = null;

        $this
            ->getAssertionManager()
            ->setHandler(
                'json',
                function ($json) use ($self, &$jsonAsserter, $jsonSchemaUriRetriever) {
                    if (null === $jsonAsserter) {
                        $jsonAsserter = new Asserter\Json($self->getAsserterGenerator());
                        $jsonAsserter->setJsonSchemaUriRetriever($jsonSchemaUriRetriever);
                    }

                    $jsonAsserter->setWithTest($self);

                    return $jsonAsserter->setWith($json, null, null);
                }
            );
    }

    /**
     * The `responsesMatch` asserter checks that a collection of responses are
     * valid regarding a collection of expected responses.
     */
    public function responsesMatch(\Generator $responses, array $expectedResponses): self
    {
        foreach ($responses as $i => $response) {
            if (!isset($expectedResponses[$i])) {
                $this->boolean(true)->isTrue();

                continue;
            }

            $expectedResponse = $expectedResponses[$i];

            $this
                ->integer($response->statusCode)
                    ->isIdenticalTo(
                        $expectedResponse['statusCode'],
                        'The expected HTTP status code is:' . "\n" .
                        '    ' . $expectedResponse['statusCode'] . "\n" .
                        'Received:' . "\n" .
                        '    ' . $response->statusCode
                    );

            if (!empty($expectedResponse['mediaType'])) {
                $this
                    ->array($response->headers)
                        ->hasKey(
                            'content-type',
                            'The expected HTTP `Content-Type` is `' .
                            $expectedResponse['mediaType'].
                            '` but this header is absent from the response.'
                        )
                        ->string['content-type']
                            ->isIdenticalTo(
                                $expectedResponse['mediaType'],
                                'The expected HTTP `Content-Type` is:' . "\n" .
                                '    ' . $expectedResponse['mediaType'] . "\n" .
                                'Received:' . "\n" .
                                '    ' . $response->headers['content-type']
                            );
            }

            if (!empty($expectedResponse['headers'])) {
                $headerAsserter = $this->array($response->headers);

                foreach ($expectedResponse['headers'] as $headerName => $headerValue) {
                    $headerAsserter
                        ->hasKey(
                            $headerName,
                            'The expected value for the HTTP header `' . $headerName . '` is `' .
                            $headerValue .
                            '` but this header is absent from the response.'
                        )
                        ->string[$headerName]
                            ->isIdenticalTo(
                                $headerValue,
                                'The expected value for the HTTP header `' . $headerName . '` is:' . "\n" .
                                '    ' . $headerValue . "\n" .
                                'Received:' . "\n" .
                                '    ' . $response->headers[$headerName]
                            );
                }
            }

            if (!empty($expectedResponse['body'])) {
                $this
                    ->string($response->body)
                        ->isIdenticalTo(
                            $expectedResponse['body'],
                            'The expected response body does not match the received one:' . "\n" .
                            new atoum\tools\diffs\variable($expectedResponse['body'], $response->body)
                        );
            }

            if (!empty($expectedResponse['schema'])) {
                $this
                    ->json($response->body)
                        ->fulfills($expectedResponse['schema']);
            }
        }

        return $this;
    }
}
