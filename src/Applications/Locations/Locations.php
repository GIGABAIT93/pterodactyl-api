<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Locations;

use Gigabait93\Applications\Locations\Builders\LocationsItemBuilder;
use Gigabait93\Applications\Locations\Builders\LocationsListBuilder;
use Gigabait93\Applications\Locations\Params\LocationParams;
use Gigabait93\Applications\Locations\Responses\LocationsListResponse;
use Gigabait93\Pterodactyl;
use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\Responses\ItemResponse;
use Gigabait93\Support\SubClient;

class Locations extends SubClient
{
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/application/locations');
    }

    public function list(): LocationsListBuilder
    {
        return new LocationsListBuilder($this, '', LocationsListResponse::class);
    }

    public function show(int $id): LocationsItemBuilder
    {
        return new LocationsItemBuilder($this, $id);
    }

    public function create(LocationParams $params): ItemResponse
    {
        $r = $this->requestResponse('POST', '', $params->toArray());

        return ItemResponse::fromBase($r);
    }

    public function update(int $id, LocationParams $params): ItemResponse
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
