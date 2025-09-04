<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Servers\Builders;

use Gigabait93\Applications\Servers\Traits\ServerIncludes;
use Gigabait93\Support\Builders\ListBuilder;

class ServersListBuilder extends ListBuilder
{
    use ServerIncludes;

    private const SORTABLE = ['id','uuid','name','created_at','updated_at'];

    // Filters
    public function filterName(string $name): self
    {
        $this->params['filter']['name'] = $name;

        return $this;
    }
    public function filterUuid(string $uuid): self
    {
        $this->params['filter']['uuid'] = $uuid;

        return $this;
    }
    public function filterExternalId(string $externalId): self
    {
        $this->params['filter']['external_id'] = $externalId;

        return $this;
    }
    public function filterImage(string $image): self
    {
        $this->params['filter']['image'] = $image;

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
