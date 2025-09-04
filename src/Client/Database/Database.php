<?php

declare(strict_types=1);

namespace Gigabait93\Client\Database;

use Gigabait93\Pterodactyl;
use Gigabait93\Support\Builders\ListBuilder;
use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\Responses\ItemResponse;
use Gigabait93\Support\Responses\ListResponse;
use Gigabait93\Support\SubClient;

class Database extends SubClient
{
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/client/servers');
    }

    public function list(string $uuidShort): ListBuilder
    {
        return new ListBuilder($this, '/' . $uuidShort . '/databases', ListResponse::class);
    }

    public function create(string $uuidShort, string $database, string $remote = '%'): ItemResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/databases', ['database' => $database, 'remote' => $remote]);

        return ItemResponse::fromBase($r);
    }

    public function resetPassword(string $uuidShort, string $database): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/databases/' . $database . '/rotate-password');

        return ActionResponse::fromBase($r);
    }

    public function destroy(string $uuidShort, string $database): ActionResponse
    {
        $r = $this->requestResponse('DELETE', '/' . $uuidShort . '/databases/' . $database);

        return ActionResponse::fromBase($r);
    }

}
