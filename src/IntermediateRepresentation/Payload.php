<?php

declare(strict_types=1);

namespace atoum\apiblueprint\IntermediateRepresentation;

class Payload
{
    /**
     * Raw body.
     *
     * See https://github.com/apiaryio/api-blueprint/blob/master/API%20Blueprint%20Specification.md#def-body-section.
     */
    public $body = '';

    /**
     * Hashmap of headers, where the keys are the header names, and the values
     * are the header values.
     *
     * See https://github.com/apiaryio/api-blueprint/blob/master/API%20Blueprint%20Specification.md#def-headers-section.
     */
    public $headers = [];

    /**
     * Schema to validate the body.
     *
     * See https://github.com/apiaryio/api-blueprint/blob/master/API%20Blueprint%20Specification.md#def-schema-section.
     */
    public $schema = '';
}
