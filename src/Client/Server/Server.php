<?php

declare(strict_types=1);

namespace Gigabait93\Client\Server;

use Gigabait93\Pterodactyl;
use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\Responses\ItemResponse;
use Gigabait93\Support\SubClient;

class Server extends SubClient
{
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/client/servers');
    }

    public function resources(string $uuidShort): ItemResponse
    {
        $r = $this->requestResponse('GET', '/' . $uuidShort . '/resources');

        return ItemResponse::fromBase($r);
    }

    public function details(string $uuidShort, array $includes = []): ItemResponse
    {
        $include = !empty($includes) ? implode(',', $includes) : 'egg,subusers';
        $r       = $this->requestResponse('GET', '/' . $uuidShort, ['include' => $include]);

        return ItemResponse::fromBase($r);
    }

    public function websocket(string $uuidShort): ItemResponse
    {
        $r = $this->requestResponse('GET', '/' . $uuidShort . '/websocket');

        return ItemResponse::fromBase($r);
    }

    public function power(string $uuidShort, string $signal): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/power', ['signal' => $signal]);

        return ActionResponse::fromBase($r);
    }

    public function command(string $uuidShort, string $command): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/command', ['command' => $command]);

        return ActionResponse::fromBase($r);
    }
}
