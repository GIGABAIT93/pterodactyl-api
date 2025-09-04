<?php

declare(strict_types=1);

namespace Gigabait93\Client\Subusers;

use Gigabait93\Pterodactyl;
use Gigabait93\Support\Builders\ListBuilder;
use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\Responses\ItemResponse;
use Gigabait93\Support\Responses\ListResponse;
use Gigabait93\Support\SubClient;

/**
 * Client API for managing server subusers.
 */
class Subusers extends SubClient
{
    /**
     * @param Pterodactyl $ptero Pterodactyl client instance
     */
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/client/servers');
    }

    /**
     * List all subusers for the given server.
     *
     * @param string $uuidShort Server identifier
     * @return ListBuilder
     */
    public function all(string $uuidShort): ListBuilder
    {
        return new ListBuilder($this, '/' . $uuidShort . '/users', ListResponse::class);
    }

    /**
     * Invite a new subuser with specific permissions.
     *
     * @param string $uuidShort Server identifier
     * @param string $email Email address of the subuser
     * @param array<int,string> $permissions Permissions to assign
     * @return ItemResponse
     */
    public function create(string $uuidShort, string $email, array $permissions): ItemResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/users', ['email' => $email, 'permissions' => array_values($permissions)]);

        return ItemResponse::fromBase($r);
    }

    /**
     * Update permissions for an existing subuser.
     *
     * @param string $uuidShort Server identifier
     * @param string $subuserUuid Subuser UUID
     * @param array<int,string> $permissions Permissions to set
     * @return ItemResponse
     */
    public function update(string $uuidShort, string $subuserUuid, array $permissions): ItemResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/users/' . $subuserUuid, ['permissions' => array_values($permissions)]);

        return ItemResponse::fromBase($r);
    }

    /**
     * Remove a subuser from the server.
     *
     * @param string $uuidShort Server identifier
     * @param string $subuserUuid Subuser UUID
     * @return ActionResponse
     */
    public function destroy(string $uuidShort, string $subuserUuid): ActionResponse
    {
        $r = $this->requestResponse('DELETE', '/' . $uuidShort . '/users/' . $subuserUuid);

        return ActionResponse::fromBase($r);
    }
}
