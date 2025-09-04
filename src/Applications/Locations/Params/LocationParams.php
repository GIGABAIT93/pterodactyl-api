<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Locations\Params;

final class LocationParams
{
    public function __construct(private string $short, private string $long)
    {
    }

    /** @return array{short:string,long:string} */
    public function toArray(): array
    {
        return ['short' => $this->short, 'long' => $this->long];
    }
}
