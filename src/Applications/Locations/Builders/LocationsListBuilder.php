<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Locations\Builders;

use Gigabait93\Applications\Locations\Traits\LocationIncludes;
use Gigabait93\Support\Builders\ListBuilder;

class LocationsListBuilder extends ListBuilder
{
    use LocationIncludes;

    private const SORTABLE = ['id','short','long','created_at','updated_at'];

    // Filters
    public function filterShort(string $short): self
    {
        $this->params['filter']['short'] = $short;

        return $this;
    }
    public function filterLong(string $long): self
    {
        $this->params['filter']['long'] = $long;

        return $this;
    }

    public function sort(string $field, bool $desc = false): self
    {
        if (!in_array($field, self::SORTABLE, true)) {
            return $this;
        }
        $this->params['sort'] = ($desc ? '-' : '') . $field;

        return $this;
    }
}
