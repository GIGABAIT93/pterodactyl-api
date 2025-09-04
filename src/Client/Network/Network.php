<?php

declare(strict_types=1);

namespace Gigabait93\Client\Network;

use Gigabait93\Pterodactyl;
use Gigabait93\Support\Builders\ListBuilder;
use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\Responses\ListResponse;
use Gigabait93\Support\SubClient;

/**
 * Client API for server network allocations.
 */
class Network extends SubClient
{
    /**
     * @param Pterodactyl $ptero Pterodactyl client instance
     */
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/client/servers');
    }

    /**
     * List network allocations for a server.
     *
     * @param string $uuidShort Server identifier
     * @return ListBuilder
     */
    public function all(string $uuidShort): ListBuilder
    {
        return new ListBuilder($this, '/' . $uuidShort . '/network/allocations', ListResponse::class);
    }

    /**
     * Assign a new allocation to the server.
     *
     * @param string $uuidShort Server identifier
     * @return ActionResponse
     */
    public function assignAllocation(string $uuidShort): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/network/allocations');

        return ActionResponse::fromBase($r);
    }

    /**
     * Set a note for an allocation.
     *
     * @param string $uuidShort Server identifier
     * @param string $allocationId Allocation identifier
     * @param string $note Note text
     * @return ActionResponse
     */
    public function setNote(string $uuidShort, string $allocationId, string $note): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/network/allocations/' . $allocationId, ['notes' => $note]);

        return ActionResponse::fromBase($r);
    }

    /**
     * Mark an allocation as primary.
     *
     * @param string $uuidShort Server identifier
     * @param string $allocationId Allocation identifier
     * @return ActionResponse
     */
    public function setPrimary(string $uuidShort, string $allocationId): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/network/allocations/' . $allocationId . '/primary');

        return ActionResponse::fromBase($r);
    }

    /**
     * Remove an allocation from the server.
     *
     * @param string $uuidShort Server identifier
     * @param string $allocationId Allocation identifier
     * @return ActionResponse
     */
    public function destroy(string $uuidShort, string $allocationId): ActionResponse
    {
        $r = $this->requestResponse('DELETE', '/' . $uuidShort . '/network/allocations/' . $allocationId);

        return ActionResponse::fromBase($r);
    }
}
