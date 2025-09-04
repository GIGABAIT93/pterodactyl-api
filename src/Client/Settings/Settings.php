<?php

declare(strict_types=1);

namespace Gigabait93\Client\Settings;

use Gigabait93\Pterodactyl;
use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\SubClient;

/**
 * Client API for server settings.
 */
class Settings extends SubClient
{
    /**
     * @param Pterodactyl $ptero Pterodactyl client instance
     */
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/client/servers');
    }

    /**
     * Rename a server.
     *
     * @param string $uuidShort Server identifier
     * @param string $name New server name
     * @return ActionResponse
     */
    public function rename(string $uuidShort, string $name): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/settings/rename', ['name' => $name]);

        return ActionResponse::fromBase($r);
    }

    /**
     * Reinstall the server.
     *
     * @param string $uuidShort Server identifier
     * @return ActionResponse
     */
    public function reinstall(string $uuidShort): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/settings/reinstall');

        return ActionResponse::fromBase($r);
    }

    /**
     * Set a custom Docker image for the server.
     *
     * @param string $uuidShort Server identifier
     * @param string $dockerImage Docker image name
     * @return ActionResponse
     */
    public function setDockerImage(string $uuidShort, string $dockerImage): ActionResponse
    {
        $r = $this->requestResponse('PUT', '/' . $uuidShort . '/settings/docker-image', ['docker_image' => $dockerImage]);

        return ActionResponse::fromBase($r);
    }
}
