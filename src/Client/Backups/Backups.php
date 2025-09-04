<?php

declare(strict_types=1);

namespace Gigabait93\Client\Backups;

use Gigabait93\Pterodactyl;
use Gigabait93\Support\Builders\ListBuilder;
use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\Responses\ItemResponse;
use Gigabait93\Support\Responses\ListResponse;
use Gigabait93\Support\SubClient;

class Backups extends SubClient
{
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/client/servers');
    }

    public function list(string $uuidShort): ListBuilder
    {
        return new ListBuilder($this, '/' . $uuidShort . '/backups', ListResponse::class);
    }

    public function show(string $uuidShort, string $backup): ItemResponse
    {
        $r = $this->requestResponse('GET', '/' . $uuidShort . '/backups/' . $backup);

        return ItemResponse::fromBase($r);
    }

    public function download(string $uuidShort, string $backup): ItemResponse
    {
        $r = $this->requestResponse('GET', '/' . $uuidShort . '/backups/' . $backup . '/download');

        return ItemResponse::fromBase($r);
    }

    public function create(string $uuidShort, string $name, string $ignoredFiles = ''): ItemResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/backups', ['name' => $name, 'ignored' => $ignoredFiles]);

        return ItemResponse::fromBase($r);
    }

    public function destroy(string $uuidShort, string $backup): ActionResponse
    {
        $r = $this->requestResponse('DELETE', '/' . $uuidShort . '/backups/' . $backup);

        return ActionResponse::fromBase($r);
    }

    public function restore(string $uuidShort, string $backup, bool $truncate = false): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/backups/' . $backup . '/restore', ['truncate' => $truncate]);

        return ActionResponse::fromBase($r);
    }

    public function lockToggle(string $uuidShort, string $backup): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/backups/' . $backup . '/lock');

        return ActionResponse::fromBase($r);
    }

}
