<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Nests;

use Gigabait93\Applications\Nests\Builders\NestsItemBuilder;
use Gigabait93\Applications\Nests\Builders\NestsListBuilder;
use Gigabait93\Applications\Nests\Responses\NestsListResponse;
use Gigabait93\Pterodactyl;
use Gigabait93\Support\SubClient;

class Nests extends SubClient
{
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/application/nests');
    }

    public function list(): NestsListBuilder
    {
        return new NestsListBuilder($this, '', NestsListResponse::class);
    }

    public function show(int $id): NestsItemBuilder
    {
        return new NestsItemBuilder($this, $id);
    }

}
