<?php

declare(strict_types=1);

namespace atoum\apiblueprint\IntermediateRepresentation;

class Document
{
    /**
     * Metadata of the document, like `FORMAT` or `HOST`.
     *
     * See https://github.com/apiaryio/api-blueprint/blob/master/API%20Blueprint%20Specification.md#metadata-section.
     */
    public $metadata  = [];

    /**
     * API name is given by the first heading of the document which is not a
     * special heading.
     *
     * See https://github.com/apiaryio/api-blueprint/blob/master/API%20Blueprint%20Specification.md#api-name--overview-section.
     */
    public $apiName   = '';

    /**
     * Resource groups.
     *
     * See https://github.com/apiaryio/api-blueprint/blob/master/API%20Blueprint%20Specification.md#resource-group-section.
     */
    public $groups    = [];

    /**
     * Resources.
     *
     * See https://github.com/apiaryio/api-blueprint/blob/master/API%20Blueprint%20Specification.md#resource-section.
     */
    public $resources = [];
}
