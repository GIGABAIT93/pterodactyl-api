<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Eggs;

use Gigabait93\Applications\Eggs\Builders\EggsItemBuilder;
use Gigabait93\Applications\Eggs\Builders\EggsListBuilder;
use Gigabait93\Applications\Eggs\Responses\EggsListResponse;
use Gigabait93\Pterodactyl;
use Gigabait93\Support\SubClient;

class Eggs extends SubClient
{
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/application/nests');
    }

    public function list(int $nestId): EggsListBuilder
    {
        return new EggsListBuilder($this, '/' . $nestId . '/eggs', EggsListResponse::class);
    }

    public function show(int $nestId, int $eggId): EggsItemBuilder
    {
        return new EggsItemBuilder($this, $nestId, $eggId);
    }

}
