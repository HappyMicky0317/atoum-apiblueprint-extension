<?php

declare(strict_types=1);

namespace atoum\apiblueprint\IntermediateRepresentation;

class Request implements Message
{
    /**
     * Name.
     */
    public $name        = '';

    /**
     * Description.
     */
    public $description = '';

    /**
     * Media type.
     */
    public $mediaType   = '';

    /**
     * Payload as a `atoum\apiblueprint\IntermediateRepresentation\Payload` object.
     */
    public $payload     = null;
}
