<?php

declare(strict_types=1);

namespace Gigabait93\Client\Backups;

use Gigabait93\Pterodactyl;
use Gigabait93\Support\Builders\ListBuilder;
use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\Responses\ItemResponse;
use Gigabait93\Support\Responses\ListResponse;
use Gigabait93\Support\SubClient;

/**
 * Client API for server backups.
 */
class Backups extends SubClient
{
    /**
     * @param Pterodactyl $ptero Pterodactyl client instance
     */
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/client/servers');
    }

    /**
     * List backups for a server.
     *
     * @param string $uuidShort Server identifier
     * @return ListBuilder
     */
    public function all(string $uuidShort): ListBuilder
    {
        return new ListBuilder($this, '/' . $uuidShort . '/backups', ListResponse::class);
    }

    /**
     * Get details for a specific backup.
     *
     * @param string $uuidShort Server identifier
     * @param string $backup Backup identifier
     * @return ItemResponse
     */
    public function get(string $uuidShort, string $backup): ItemResponse
    {
        $r = $this->requestResponse('GET', '/' . $uuidShort . '/backups/' . $backup);

        return ItemResponse::fromBase($r);
    }

    /**
     * Generate a download link for a backup.
     *
     * @param string $uuidShort Server identifier
     * @param string $backup Backup identifier
     * @return ItemResponse
     */
    public function download(string $uuidShort, string $backup): ItemResponse
    {
        $r = $this->requestResponse('GET', '/' . $uuidShort . '/backups/' . $backup . '/download');

        return ItemResponse::fromBase($r);
    }

    /**
     * Create a new backup.
     *
     * @param string $uuidShort Server identifier
     * @param string $name Backup name
     * @param string $ignoredFiles Paths to ignore
     * @return ItemResponse
     */
    public function create(string $uuidShort, string $name, string $ignoredFiles = ''): ItemResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/backups', ['name' => $name, 'ignored' => $ignoredFiles]);

        return ItemResponse::fromBase($r);
    }

    /**
     * Delete a backup.
     *
     * @param string $uuidShort Server identifier
     * @param string $backup Backup identifier
     * @return ActionResponse
     */
    public function destroy(string $uuidShort, string $backup): ActionResponse
    {
        $r = $this->requestResponse('DELETE', '/' . $uuidShort . '/backups/' . $backup);

        return ActionResponse::fromBase($r);
    }

    /**
     * Restore a backup.
     *
     * @param string $uuidShort Server identifier
     * @param string $backup Backup identifier
     * @param bool $truncate Whether to truncate server files first
     * @return ActionResponse
     */
    public function restore(string $uuidShort, string $backup, bool $truncate = false): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/backups/' . $backup . '/restore', ['truncate' => $truncate]);

        return ActionResponse::fromBase($r);
    }

    /**
     * Toggle backup lock state.
     *
     * @param string $uuidShort Server identifier
     * @param string $backup Backup identifier
     * @return ActionResponse
     */
    public function lockToggle(string $uuidShort, string $backup): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/backups/' . $backup . '/lock');

        return ActionResponse::fromBase($r);
    }
}
