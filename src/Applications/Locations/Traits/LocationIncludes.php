<?php

namespace Gigabait93\Applications\Locations\Traits;

use Gigabait93\Applications\Locations\Enums\LocationsInclude;

trait LocationIncludes
{
    public function includeNodes(): self
    {
        return $this->includes(LocationsInclude::Nodes);
    }

    public function includeServers(): self
    {
        return $this->includes(LocationsInclude::Servers);
    }
}
