<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Nodes;

use Gigabait93\Applications\Nodes\Builders\NodesItemBuilder;
use Gigabait93\Applications\Nodes\Builders\NodesListBuilder;
use Gigabait93\Applications\Nodes\Params\NodeCreateParams;
use Gigabait93\Applications\Nodes\Params\NodeUpdateParams;
use Gigabait93\Applications\Nodes\Responses\NodesListResponse;
use Gigabait93\Pterodactyl;
use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\Responses\ItemResponse;
use Gigabait93\Support\SubClient;

class Nodes extends SubClient
{
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/application/nodes');
    }

    public function all(): NodesListBuilder
    {
        return new NodesListBuilder($this, '', NodesListResponse::class);
    }

    public function get(int $id): NodesItemBuilder
    {
        return new NodesItemBuilder($this, $id);
    }

    public function create(NodeCreateParams $params): ItemResponse
    {
        $r = $this->requestResponse('POST', '', $params->toArray());

        return ItemResponse::fromBase($r);
    }

    public function update(int $id, NodeUpdateParams $params): ItemResponse
    {
        $r = $this->requestResponse('PATCH', '/' . $id, $params->toArray());

        return ItemResponse::fromBase($r);
    }

    public function destroy(int $id): ActionResponse
    {
        $r = $this->requestResponse('DELETE', '/' . $id);

        return ActionResponse::fromBase($r);
    }

}
