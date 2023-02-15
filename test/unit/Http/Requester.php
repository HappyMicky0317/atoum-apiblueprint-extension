<?php

declare(strict_types=1);

namespace atoum\apiblueprint\test\unit\Http;

use atoum\apiblueprint\Http\Requester as SUT;
use atoum\apiblueprint\Http\Response;
use mageekguy\atoum\test;

class Requester extends test
{
    public function test_add_request()
    {
        $self = $this;

        $this
            ->given(
                $method  = 'GET',
                $url     = '/foo/bar',
                $headers = ['Baz' => 'Qux'],

                $requester = new SUT(),

                $this->function->curl_multi_init->doesNothing(),
                $this->function->curl_init->doesNothing(),
                $this->function->curl_setopt_array = function ($curlHandler, array $options) use ($self, $method, $url) {
                    $this
                        ->array($options)
                            ->isEqualTo([
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_HEADER         => true,
                                CURLOPT_CUSTOMREQUEST  => $method,
                                CURLOPT_URL            => $url,
                                CURLOPT_HTTPHEADER     => ['Baz: Qux']
                            ]);
                },
                $this->function->curl_multi_add_handle->doesNothing()
            )
            ->when($result = $requester->addRequest($method, $url, $headers))
            ->then
                ->variable($result)
                    ->isNull()
                ->function('curl_init')
                    ->once()
                ->function('curl_setopt_array')
                    ->once()
                ->function('curl_init')
                    ->once();
    }

    public function test_send()
    {
        $self = $this;

        $this
            ->given(
                $method  = 'GET',
                $url     = '/foo/bar',
                $headers = ['foo' => 'bar'],

                $this->function->curl_multi_init->doesNothing(),
                $this->function->curl_init->doesNothing(),
                $this->function->curl_setopt_array->doesNothing(),
                $this->function->curl_multi_add_handle->doesNothing(),

                $this->function->curl_multi_exec = function ($curlMultiHandler, &$running) {
                    $running = -1;
                },
                $this->function->curl_multi_select->doesNothing(),

                $this->function->curl_getinfo = function ($curlHandler, $info) use ($url) {
                    switch ($info) {
                        case CURLINFO_HEADER_SIZE:
                            return 7;

                        case CURLINFO_HTTP_CODE:
                            return 123;

                        case CURLINFO_EFFECTIVE_URL:
                            return $url;
                    }
                },
                $this->function->curl_multi_getcontent = 'foo:barHello, World!',

                $this->function->curl_multi_remove_handle->doesNothing(),
                $this->function->curl_close->doesNothing(),
                $this->function->curl_multi_close->doesNothing(),

                $requester = new SUT(),
                $requester->addRequest($method, $url, $headers),
                $requester->addRequest($method, $url, $headers)
            )
            ->when($result = $requester->send())
            ->then
                ->generator($result)
                    ->yields
                        ->object
                            ->isInstanceOf(Response::class)
                            ->isEqualTo(new Response(123, $url, $headers, 'Hello, World!'))
                    ->yields
                        ->object
                            ->isInstanceOf(Response::class)
                            ->isEqualTo(new Response(123, $url, $headers, 'Hello, World!'))
                ->function('curl_multi_exec')
                    ->once()
                ->function('curl_multi_select')
                    ->once();
    }
}
