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

/**
 * Application API for managing servers.
 */
class Servers extends SubClient
{
    use ServerActions;

    /**
     * @param Pterodactyl $ptero Pterodactyl client instance
     */
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/application/servers');
    }

    /**
     * List all servers.
     *
     * @return ServersListBuilder
     */
    public function all(): ServersListBuilder
    {
        return new ServersListBuilder($this, '', ServersListResponse::class);
    }

    /**
     * Get server information by ID.
     *
     * @param int $id Server identifier
     * @return ServersItemBuilder
     */
    public function get(int $id): ServersItemBuilder
    {
        return new ServersItemBuilder($this, $id);
    }

    /**
     * Get server information by external ID.
     *
     * @param string $externalId External identifier
     * @return ServersItemBuilder
     */
    public function external(string $externalId): ServersItemBuilder
    {
        return new ServersItemBuilder($this, 'external/' . $externalId);
    }

    /**
     * Update server details.
     *
     * @param int $id Server identifier
     * @param ServerDetailsParams $params Parameters
     * @return ItemResponse
     */
    public function update(int $id, ServerDetailsParams $params): ItemResponse
    {
        return $this->doUpdateServerDetails($id, $params->toArray());
    }

    /**
     * Modify server build configuration.
     *
     * @param int $id Server identifier
     * @param ServerBuildParams $params Parameters
     * @return ItemResponse
     */
    public function build(int $id, ServerBuildParams $params): ItemResponse
    {
        return $this->doBuildServer($id, $params->toArray());
    }

    /**
     * Update server startup variables.
     *
     * @param int $id Server identifier
     * @param ServerStartupParams $params Parameters
     * @return ItemResponse
     */
    public function startup(int $id, ServerStartupParams $params): ItemResponse
    {
        return $this->doStartupServer($id, $params->toArray());
    }

    /**
     * Create a new server.
     *
     * @param CreateServerParams $params Parameters
     * @return ItemResponse
     */
    public function create(CreateServerParams $params): ItemResponse
    {
        $r = $this->requestResponse('POST', '', $params->toArray());

        return ItemResponse::fromBase($r);
    }

    /**
     * Suspend a server.
     *
     * @param int $id Server identifier
     * @return ActionResponse
     */
    public function suspend(int $id): ActionResponse
    {
        return $this->doSuspendServer($id);
    }

    /**
     * Unsuspend a server.
     *
     * @param int $id Server identifier
     * @return ActionResponse
     */
    public function unsuspend(int $id): ActionResponse
    {
        return $this->doUnsuspendServer($id);
    }

    /**
     * Reinstall a server.
     *
     * @param int $id Server identifier
     * @return ActionResponse
     */
    public function reinstall(int $id): ActionResponse
    {
        return $this->doReinstallServer($id);
    }

    /**
     * Delete a server.
     *
     * @param int $id Server identifier
     * @return ActionResponse
     */
    public function destroy(int $id): ActionResponse
    {
        return $this->doDestroyServer($id);
    }

    /**
     * Force delete a server.
     *
     * @param int $id Server identifier
     * @return ActionResponse
     */
    public function forceDelete(int $id): ActionResponse
    {
        return $this->doForceDeleteServer($id);
    }

    /**
     * Find a server by UUID.
     *
     * @param string $uuid UUID of the server
     * @return ItemResponse
     */
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
