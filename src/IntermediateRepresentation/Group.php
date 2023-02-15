<?php

declare(strict_types=1);

namespace atoum\apiblueprint\IntermediateRepresentation;

class Group
{
    /**
     * Group name.
     */
    public $name      = '';

    /**
     * Resources.
     *
     * See https://github.com/apiaryio/api-blueprint/blob/master/API%20Blueprint%20Specification.md#resource-section.
     */
    public $resources = [];
}
