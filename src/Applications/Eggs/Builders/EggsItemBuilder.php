<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Eggs\Builders;

use Gigabait93\Applications\Eggs\Traits\EggIncludes;
use Gigabait93\Support\Builders\GetBuilder;
use Gigabait93\Support\Responses\ItemResponse;
use Gigabait93\Support\SubClient;

class EggsItemBuilder extends GetBuilder
{
    use EggIncludes;

    public function __construct(SubClient $client, int $nestId, int $eggId, string $responseClass = ItemResponse::class)
    {
        parent::__construct($client, '/' . $nestId . '/eggs/' . $eggId, $responseClass);
    }
}
