<?php

declare(strict_types=1);

namespace atoum\apiblueprint\IntermediateRepresentation;

class Response implements Message
{
    /**
     * Description.
     */
    public $description = '';

    /**
     * Status code as an integer.
     */
    public $statusCode  = 200;

    /**
     * Media type.
     */
    public $mediaType   = '';

    /**
     * Payload as a `atoum\apiblueprint\IntermediateRepresentation\Payload` object.
     */
    public $payload     = null;
}
