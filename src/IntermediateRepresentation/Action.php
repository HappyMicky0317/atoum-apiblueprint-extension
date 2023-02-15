<?php

declare(strict_types=1);

namespace atoum\apiblueprint\IntermediateRepresentation;

class Action
{
    /**
     * Action name.
     */
    public $name          = '';

    /**
     * Request method.
     */
    public $requestMethod = 'GET';

    /**
     * URI template.
     */
    public $uriTemplate   = '';

    /**
     * Sequence of requests and responses.
     */
    public $messages      = [];
}
