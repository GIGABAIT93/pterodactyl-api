<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Servers;

use Gigabait93\Applications\Servers\Builders\ServersItemBuilder;
use Gigabait93\Applications\Servers\Builders\ServersListBuilder;
use Gigabait93\Applications\Servers\Params\CreateServerParams;
use Gigabait93\Applications\Servers\Params\ServerBuildParams;
use Gigabait93\Applications\Servers\Params\ServerDetailsParams;
use Gigabait93\Applications\Servers\Params\ServerStartupParams;
use Gigabait93\Applications\Servers\Responses\ServersListResponse;
use Gigabait93\Applications\Servers\Traits\ServerActions;
use Gigabait93\Pterodactyl;
use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\Responses\ItemResponse;
use Gigabait93\Support\SubClient;

class Servers extends SubClient
{
    use ServerActions;

    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/application/servers');
    }

    public function list(): ServersListBuilder
    {
        return new ServersListBuilder($this, '', ServersListResponse::class);
    }

    public function show(int $id): ServersItemBuilder
    {
        return new ServersItemBuilder($this, $id);
    }

    public function external(string $externalId): ServersItemBuilder
    {
        return new ServersItemBuilder($this, 'external/' . $externalId);
    }

    public function update(int $id, ServerDetailsParams $params): ItemResponse
    {
        return $this->doUpdateServerDetails($id, $params->toArray());
    }

    public function build(int $id, ServerBuildParams $params): ItemResponse
    {
        return $this->doBuildServer($id, $params->toArray());
    }

    public function startup(int $id, ServerStartupParams $params): ItemResponse
    {
        return $this->doStartupServer($id, $params->toArray());
    }

    public function create(CreateServerParams $params): ItemResponse
    {
        $r = $this->requestResponse('POST', '', $params->toArray());

        return ItemResponse::fromBase($r);
    }

    public function suspend(int $id): ActionResponse
    {
        return $this->doSuspendServer($id);
    }

    public function unsuspend(int $id): ActionResponse
    {
        return $this->doUnsuspendServer($id);
    }

    public function reinstall(int $id): ActionResponse
    {
        return $this->doReinstallServer($id);
    }

    public function destroy(int $id): ActionResponse
    {
        return $this->doDestroyServer($id);
    }

    public function forceDelete(int $id): ActionResponse
    {
        return $this->doForceDeleteServer($id);
    }

    public function getUuid(string $uuid): ItemResponse
    {
        $servers = $this->allPages('');
        foreach ($servers as $server) {
            if (($server['attributes']['uuid'] ?? null) === $uuid) {
                return new ItemResponse(true, 200, [], $server, null, null);
            }
        }

        return new ItemResponse(false, 404, [], null, "Server with UUID {$uuid} not found", null);
    }

    /** Provide SubClient for ServerActions trait. */
    protected function serverClient(): SubClient
    {
        return $this;
    }
}
