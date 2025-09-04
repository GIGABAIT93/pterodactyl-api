<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Nodes\Builders;

use Gigabait93\Applications\Nodes\Traits\NodeIncludes;
use Gigabait93\Support\Builders\ListBuilder;

/**
 * Builder for nests list endpoint with convenience include helpers.
 */
class NodesListBuilder extends ListBuilder
{
    use NodeIncludes;

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
    public function filterFqdn(string $fqdn): self
    {
        $this->params['filter']['fqdn'] = $fqdn;

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
