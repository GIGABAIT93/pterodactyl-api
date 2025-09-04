<?php

declare(strict_types=1);

namespace Gigabait93\Client\Database;

use Gigabait93\Pterodactyl;
use Gigabait93\Support\Builders\ListBuilder;
use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\Responses\ItemResponse;
use Gigabait93\Support\Responses\ListResponse;
use Gigabait93\Support\SubClient;

/**
 * Client API for managing server databases.
 */
class Database extends SubClient
{
    /**
     * @param Pterodactyl $ptero Pterodactyl client instance
     */
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/client/servers');
    }

    /**
     * List databases belonging to a server.
     *
     * @param string $uuidShort Server identifier
     * @return ListBuilder
     */
    public function all(string $uuidShort): ListBuilder
    {
        return new ListBuilder($this, '/' . $uuidShort . '/databases', ListResponse::class);
    }

    /**
     * Create a new database for the server.
     *
     * @param string $uuidShort Server identifier
     * @param string $database Database name
     * @param string $remote Remote host restriction
     * @return ItemResponse
     */
    public function create(string $uuidShort, string $database, string $remote = '%'): ItemResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/databases', ['database' => $database, 'remote' => $remote]);

        return ItemResponse::fromBase($r);
    }

    /**
     * Rotate the password for a database.
     *
     * @param string $uuidShort Server identifier
     * @param string $database Database identifier
     * @return ActionResponse
     */
    public function resetPassword(string $uuidShort, string $database): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/databases/' . $database . '/rotate-password');

        return ActionResponse::fromBase($r);
    }

    /**
     * Delete a database from the server.
     *
     * @param string $uuidShort Server identifier
     * @param string $database Database identifier
     * @return ActionResponse
     */
    public function destroy(string $uuidShort, string $database): ActionResponse
    {
        $r = $this->requestResponse('DELETE', '/' . $uuidShort . '/databases/' . $database);

        return ActionResponse::fromBase($r);
    }
}
