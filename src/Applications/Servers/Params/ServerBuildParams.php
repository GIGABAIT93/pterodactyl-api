<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Servers\Params;

/**
 * DTO for PATCH /servers/{id}/build
 */
final class ServerBuildParams
{
    private ?int $allocation = null;

    // limits
    private ?int $memory       = null;   // MB
    private ?int $swap         = null;     // MB (-1 unlimited)
    private ?int $disk         = null;     // MB
    private ?int $io           = null;       // 10-1000
    private ?int $cpu          = null;      // %*100
    private ?string $threads   = null; // e.g., "0-1,3"
    private ?bool $oomDisabled = null;

    // features
    private ?int $db      = null;
    private ?int $allocs  = null;
    private ?int $backups = null;

    public function allocation(int $id): self
    {
        $this->allocation = $id;

        return $this;
    }

    public function limits(
        ?int $memory = null,
        ?int $swap = null,
        ?int $disk = null,
        ?int $io = null,
        ?int $cpu = null,
        ?string $threads = null,
        ?bool $oomDisabled = null
    ): self {
        if ($memory !== null) {
            $this->memory = $memory;
        }
        if ($swap !== null) {
            $this->swap = $swap;
        }
        if ($disk !== null) {
            $this->disk = $disk;
        }
        if ($io !== null) {
            $this->io = $io;
        }
        if ($cpu !== null) {
            $this->cpu = $cpu;
        }
        if ($threads !== null) {
            $this->threads = $threads;
        }
        if ($oomDisabled !== null) {
            $this->oomDisabled = $oomDisabled;
        }

        return $this;
    }

    public function features(?int $databases = null, ?int $allocations = null, ?int $backups = null): self
    {
        if ($databases !== null) {
            $this->db = $databases;
        }
        if ($allocations !== null) {
            $this->allocs = $allocations;
        }
        if ($backups !== null) {
            $this->backups = $backups;
        }

        return $this;
    }

    /**
     * @return array{
     *   allocation?:int,
     *   limits?:array{memory?:int,swap?:int,disk?:int,io?:int,cpu?:int,threads?:string,oom_disabled?:bool},
     *   feature_limits?:array{databases?:int,allocations?:int,backups?:int}
     * }
     */
    public function toArray(): array
    {
        $out = [];
        if ($this->allocation !== null) {
            $out['allocation'] = $this->allocation;
        }

        $limits = [];
        if ($this->memory !== null) {
            $limits['memory'] = $this->memory;
        }
        if ($this->swap !== null) {
            $limits['swap'] = $this->swap;
        }
        if ($this->disk !== null) {
            $limits['disk'] = $this->disk;
        }
        if ($this->io !== null) {
            $limits['io'] = $this->io;
        }
        if ($this->cpu !== null) {
            $limits['cpu'] = $this->cpu;
        }
        if ($this->threads !== null) {
            $limits['threads'] = $this->threads;
        }
        if ($this->oomDisabled !== null) {
            $limits['oom_disabled'] = $this->oomDisabled;
        }
        if ($limits !== []) {
            $out['limits'] = $limits;
        }

        $feat = [];
        if ($this->db !== null) {
            $feat['databases'] = $this->db;
        }
        if ($this->allocs !== null) {
            $feat['allocations'] = $this->allocs;
        }
        if ($this->backups !== null) {
            $feat['backups'] = $this->backups;
        }
        if ($feat !== []) {
            $out['feature_limits'] = $feat;
        }

        return $out;
    }
}
