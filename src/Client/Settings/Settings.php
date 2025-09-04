<?php

declare(strict_types=1);

namespace Gigabait93\Client\Settings;

use Gigabait93\Pterodactyl;
use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\SubClient;

class Settings extends SubClient
{
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/client/servers');
    }

    public function rename(string $uuidShort, string $name): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/settings/rename', ['name' => $name]);

        return ActionResponse::fromBase($r);
    }

    public function reinstall(string $uuidShort): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/settings/reinstall');

        return ActionResponse::fromBase($r);
    }

    public function setDockerImage(string $uuidShort, string $dockerImage): ActionResponse
    {
        $r = $this->requestResponse('PUT', '/' . $uuidShort . '/settings/docker-image', ['docker_image' => $dockerImage]);

        return ActionResponse::fromBase($r);
    }
}
