<?php

declare(strict_types=1);

namespace Gigabait93\Client\Startup;

use Gigabait93\Pterodactyl;
use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\Responses\ItemResponse;
use Gigabait93\Support\SubClient;

class Startup extends SubClient
{
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/client/servers');
    }

    public function variables(string $uuidShort): ItemResponse
    {
        $r = $this->requestResponse('GET', '/' . $uuidShort . '/startup');

        return ItemResponse::fromBase($r);
    }

    public function update(string $uuidShort, array $data): ActionResponse
    {
        $r = $this->requestResponse('PUT', '/' . $uuidShort . '/startup/variable', $data);

        return ActionResponse::fromBase($r);
    }
}
