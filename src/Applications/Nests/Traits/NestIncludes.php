<?php

namespace Gigabait93\Applications\Nests\Traits;

use Gigabait93\Applications\Nests\Enums\NestsInclude;

trait NestIncludes
{
    public function includeEggs(): self
    {
        return $this->includes(NestsInclude::Eggs);
    }

    public function includeServers(): self
    {
        return $this->includes(NestsInclude::Servers);
    }
}
