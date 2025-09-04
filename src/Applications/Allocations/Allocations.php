<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Allocations;

use Gigabait93\Pterodactyl;
use Gigabait93\Support\Builders\ListBuilder;
use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\Responses\ListResponse;
use Gigabait93\Support\SubClient;

class Allocations extends SubClient
{
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/application/nodes');
    }

    public function list(int $nodeId): ListBuilder
    {
        return new ListBuilder($this, '/' . $nodeId . '/allocations', ListResponse::class);
    }

    /** @param array{ip:string,ports:array<int>} $params */
    public function create(int $nodeId, array $params): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $nodeId . '/allocations', $params);

        return ActionResponse::fromBase($r);
    }

    public function destroy(int $nodeId, int $allocationId): ActionResponse
    {
        $r = $this->requestResponse('DELETE', '/' . $nodeId . '/allocations/' . $allocationId);

        return ActionResponse::fromBase($r);
    }

}
