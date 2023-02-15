<?php

declare(strict_types=1);

namespace atoum\apiblueprint\IntermediateRepresentation;

class Resource
{
    /**
     * Resource name.
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
     * Actions.
     *
     * See https://github.com/apiaryio/api-blueprint/blob/master/API%20Blueprint%20Specification.md#def-action-section.
     */
    public $actions       = [];
}
