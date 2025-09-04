<?php

declare(strict_types=1);

namespace Gigabait93\Client\Subusers;

use Gigabait93\Pterodactyl;
use Gigabait93\Support\Builders\ListBuilder;
use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\Responses\ItemResponse;
use Gigabait93\Support\Responses\ListResponse;
use Gigabait93\Support\SubClient;

class Subusers extends SubClient
{
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/client/servers');
    }

    public function list(string $uuidShort): ListBuilder
    {
        return new ListBuilder($this, '/' . $uuidShort . '/users', ListResponse::class);
    }

    public function create(string $uuidShort, string $email, array $permissions): ItemResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/users', ['email' => $email, 'permissions' => array_values($permissions)]);

        return ItemResponse::fromBase($r);
    }

    public function update(string $uuidShort, string $subuserUuid, array $permissions): ItemResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/users/' . $subuserUuid, ['permissions' => array_values($permissions)]);

        return ItemResponse::fromBase($r);
    }

    public function destroy(string $uuidShort, string $subuserUuid): ActionResponse
    {
        $r = $this->requestResponse('DELETE', '/' . $uuidShort . '/users/' . $subuserUuid);

        return ActionResponse::fromBase($r);
    }

}
