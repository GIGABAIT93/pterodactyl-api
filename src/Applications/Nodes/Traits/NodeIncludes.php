<?php

namespace Gigabait93\Applications\Nodes\Traits;

use Gigabait93\Applications\Nodes\Enums\NodesInclude;

trait NodeIncludes
{
    /** Include eggs relation. */
    public function includeServers(): self
    {
        return $this->includes(NodesInclude::Servers);
    }

    public function includeLocation(): self
    {
        return $this->includes(NodesInclude::Location);
    }

    public function includeAllocations(): self
    {
        return $this->includes(NodesInclude::Allocations);
    }
}
