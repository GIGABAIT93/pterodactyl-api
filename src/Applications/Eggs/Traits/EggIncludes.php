<?php

namespace Gigabait93\Applications\Eggs\Traits;

use Gigabait93\Applications\Eggs\Enums\EggsInclude;

trait EggIncludes
{
    public function includeNest(): self
    {
        return $this->includes(EggsInclude::Nest);
    }

    public function includeVariables(): self
    {
        return $this->includes(EggsInclude::Variables);
    }

    public function includeServers(): self
    {
        return $this->includes(EggsInclude::Servers);
    }

    public function includeConfig(): self
    {
        return $this->includes(EggsInclude::Config);
    }

    public function includeScript(): self
    {
        return $this->includes(EggsInclude::Script);
    }
}
