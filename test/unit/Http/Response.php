<?php

declare(strict_types=1);

namespace atoum\apiblueprint\test\unit\Http;

use atoum\apiblueprint\Http\Response as SUT;
use mageekguy\atoum\test;

class Response extends test
{
    public function test_constructor()
    {
        $this
            ->given(
                $statusCode = 123,
                $url        = '/foo/bar',
                $headers    = ['baz' => 'qux'],
                $body       = 'Hello, World!'
            )
            ->when($result = new SUT($statusCode, $url, $headers, $body))
            ->then
                ->integer($result->statusCode)
                    ->isIdenticalTo($statusCode)
                ->string($result->url)
                    ->isIdenticalTo($url)
                ->array($result->headers)
                    ->isIdenticalTo($headers)
                ->string($result->body)
                    ->isIdenticalTo($body);
    }

    public function test_from_curl_response()
    {
        $this
            ->given(
                $statusCode = 123,
                $url        = '/foo/bar',
                $headers    = 'Baz: Qux',
                $body       = 'Hello, World!'
            )
            ->when($result = SUT::fromCurlResponse($statusCode, $url, $headers, $body))
            ->then
                ->integer($result->statusCode)
                    ->isIdenticalTo($statusCode)
                ->string($result->url)
                    ->isIdenticalTo($url)
                ->array($result->headers)
                    ->isIdenticalTo([
                        'baz' => 'Qux'
                    ])
                ->string($result->body)
                    ->isIdenticalTo($body);
    }

    public function test_parse_empty_raw_headers()
    {
        $this
            ->given($headers = '')
            ->when($result = SUT::parseRawHeaders($headers))
            ->then
                ->array($result)
                    ->isEmpty();
    }

    public function test_parse_raw_headers()
    {
        $this
            ->given($headers = 'Foo:Bar' . "\r\n" . 'Baz:     Qux' . "\r\n")
            ->when($result = SUT::parseRawHeaders($headers))
            ->then
                ->array($result)
                    ->isEqualTo([
                        'foo' => 'Bar',
                        'baz' => 'Qux'
                    ]);
    }
}
