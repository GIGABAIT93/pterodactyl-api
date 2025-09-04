<?php

declare(strict_types=1);

namespace Gigabait93\Support\Builders;

use Gigabait93\Support\Responses\ItemResponse;
use Gigabait93\Support\SubClient;

class ItemBuilder extends GetBuilder
{
    public readonly int|string $id;

    public function __construct(SubClient $client, int|string $id, string $responseClass = ItemResponse::class)
    {
        $this->id = $id;
        parent::__construct($client, '/' . $id, $responseClass);
    }

    public function getId(): int|string
    {
        return $this->id;
    }
}
