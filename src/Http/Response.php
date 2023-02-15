<?php

declare(strict_types=1);

namespace atoum\apiblueprint\Http;

class Response
{
    public $statusCode = 0;
    public $url        = '';
    public $headers    = [];
    public $body       = '';

    public function __construct(int $statusCode, string $url, array $headers, string $body)
    {
        $this->statusCode = $statusCode;
        $this->url        = $url;
        $this->headers    = $headers;
        $this->body       = $body;
    }

    public static function fromCurlResponse(int $statusCode, string $url, string $headers, string $body)
    {
        return new static($statusCode, $url, static::parseRawHeaders($headers), $body);
    }

    public static function parseRawHeaders(string $headers): array
    {
        $out = [];

        foreach (preg_split('/\v+/', $headers) as $line) {
            if (false === $pos = strpos($line, ':')) {
                continue;
            }

            $out[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos + 1));
        }

        return $out;
    }
}
