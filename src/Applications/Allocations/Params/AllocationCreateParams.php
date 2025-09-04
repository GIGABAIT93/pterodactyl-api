<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Allocations\Params;

final class AllocationCreateParams
{
    /** @param array<int,int> $ports */
    public function __construct(private string $ip, private array $ports)
    {
    }

    /** @return array{ip:string,ports:array<int,int>} */
    public function toArray(): array
    {
        return ['ip' => $this->ip, 'ports' => array_values($this->ports)];
    }
}
