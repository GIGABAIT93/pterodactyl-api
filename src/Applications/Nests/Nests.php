<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Nests;

use Gigabait93\Applications\Nests\Builders\NestsItemBuilder;
use Gigabait93\Applications\Nests\Builders\NestsListBuilder;
use Gigabait93\Applications\Nests\Responses\NestsListResponse;
use Gigabait93\Pterodactyl;
use Gigabait93\Support\SubClient;

/**
 * Application API for panel nests.
 */
class Nests extends SubClient
{
    /**
     * @param Pterodactyl $ptero Pterodactyl client instance
     */
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/application/nests');
    }

    /**
     * List all nests.
     *
     * @return NestsListBuilder
     */
    public function all(): NestsListBuilder
    {
        return new NestsListBuilder($this, '', NestsListResponse::class);
    }

    /**
     * Get a nest by its identifier.
     *
     * @param int $id Nest identifier
     * @return NestsItemBuilder
     */
    public function get(int $id): NestsItemBuilder
    {
        return new NestsItemBuilder($this, $id);
    }
}
