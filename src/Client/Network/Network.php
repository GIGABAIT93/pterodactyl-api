<?php

declare(strict_types=1);

namespace Gigabait93\Client\Network;

use Gigabait93\Pterodactyl;
use Gigabait93\Support\Builders\ListBuilder;
use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\Responses\ListResponse;
use Gigabait93\Support\SubClient;

class Network extends SubClient
{
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/client/servers');
    }

    public function list(string $uuidShort): ListBuilder
    {
        return new ListBuilder($this, '/' . $uuidShort . '/network/allocations', ListResponse::class);
    }

    public function assignAllocation(string $uuidShort): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/network/allocations');

        return ActionResponse::fromBase($r);
    }

    public function setNote(string $uuidShort, string $allocationId, string $note): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/network/allocations/' . $allocationId, ['notes' => $note]);

        return ActionResponse::fromBase($r);
    }

    public function setPrimary(string $uuidShort, string $allocationId): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/network/allocations/' . $allocationId . '/primary');

        return ActionResponse::fromBase($r);
    }

    public function destroy(string $uuidShort, string $allocationId): ActionResponse
    {
        $r = $this->requestResponse('DELETE', '/' . $uuidShort . '/network/allocations/' . $allocationId);

        return ActionResponse::fromBase($r);
    }

}
