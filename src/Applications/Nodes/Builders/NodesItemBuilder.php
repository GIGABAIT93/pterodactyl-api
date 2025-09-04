<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Nodes\Builders;

use Gigabait93\Applications\Nodes\Traits\NodeIncludes;
use Gigabait93\Support\Builders\ItemBuilder;
use Gigabait93\Support\Builders\ListBuilder;
use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\Responses\ItemResponse;

/**
 * Builder for nests list endpoint with convenience include helpers.
 */
class NodesItemBuilder extends \Gigabait93\Support\Builders\ItemBuilder
{
    use NodeIncludes;

    public function allocations(): ListBuilder
    {
        return $this->client->ptero()->allocations->list($this->getId());
    }

    public function createAllocation(array $params): ActionResponse
    {
        return $this->client->ptero()->allocations->create($this->getId(), $params);
    }

    public function deleteAllocation(int $allocationId): ActionResponse
    {
        return $this->client->ptero()->allocations->destroy($this->getId(), $allocationId);
    }

    public function configuration(): \Gigabait93\Support\Builders\ItemBuilder
    {
        return new ItemBuilder($this->client, '/' . $this->getId() . '/configuration', ItemResponse::class);
    }
}
