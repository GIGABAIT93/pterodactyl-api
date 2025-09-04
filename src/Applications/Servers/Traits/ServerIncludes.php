<?php

namespace Gigabait93\Applications\Servers\Traits;

use Gigabait93\Applications\Servers\Enums\ServersInclude;

trait ServerIncludes
{
    public function includeEgg(): self
    {
        return $this->includes(ServersInclude::Egg);
    }

    public function includeNest(): self
    {
        return $this->includes(ServersInclude::Nest);
    }

    public function includeAllocations(): self
    {
        return $this->includes(ServersInclude::Allocations);
    }

    public function includeUser(): self
    {
        return $this->includes(ServersInclude::User);
    }

    public function includeNode(): self
    {
        return $this->includes(ServersInclude::Node);
    }

    public function includeLocation(): self
    {
        return $this->includes(ServersInclude::Location);
    }
}
