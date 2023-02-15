<?php

declare(strict_types=1);

namespace atoum\apiblueprint\Http;

class Requester
{
    protected $_curlMultiHandler = null;
    protected $_curlHandlers     = [];

    public function addRequest(string $method, string $url, array $headers = [])
    {
        if (null === $this->_curlMultiHandler) {
            $this->_curlMultiHandler = curl_multi_init();
        }

        $curlHandler           = curl_init();
        $this->_curlHandlers[] = $curlHandler;

        $formattedHeaders = [];

        foreach ($headers as $headerName => $headerValue) {
            $formattedHeaders[] = $headerName . ': ' . $headerValue;
        }

        curl_setopt_array(
            $curlHandler,
            [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER         => true,
                CURLOPT_CUSTOMREQUEST  => $method,
                CURLOPT_URL            => $url,
                CURLOPT_HTTPHEADER     => $formattedHeaders
            ]
        );

        curl_multi_add_handle($this->_curlMultiHandler, $curlHandler);
    }

    public function send(): \Generator
    {
        $running = null;

        do {
            curl_multi_exec($this->_curlMultiHandler, $running);
            curl_multi_select($this->_curlMultiHandler);
        } while (0 < $running);

        foreach ($this->_curlHandlers as $curlHandler) {
            $headerSize = curl_getinfo($curlHandler, CURLINFO_HEADER_SIZE);
            $response   = curl_multi_getcontent($curlHandler);

            yield Response::fromCurlResponse(
                curl_getinfo($curlHandler, CURLINFO_HTTP_CODE),
                curl_getinfo($curlHandler, CURLINFO_EFFECTIVE_URL),
                substr($response, 0, $headerSize),
                substr($response, $headerSize)
            );

            curl_multi_remove_handle($this->_curlMultiHandler, $curlHandler);
            curl_close($curlHandler);
        }

        curl_multi_close($this->_curlMultiHandler);
    }
}
