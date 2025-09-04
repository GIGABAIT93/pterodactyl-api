<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Nodes\Params;

final class NodeUpdateParams
{
    private ?string $name      = null;
    private ?int $locationId   = null;
    private ?string $fqdn      = null;
    private ?string $scheme    = null;
    private ?bool $behindProxy = null;
    private ?bool $maintenance = null;

    private ?int $memory             = null;
    private ?int $memoryOverallocate = null;
    private ?int $disk               = null;
    private ?int $diskOverallocate   = null;
    private ?int $uploadSize         = null;
    private ?int $daemonListen       = null;
    private ?int $daemonSftp         = null;
    private ?string $description     = null;

    public function name(string $v): self
    {
        $this->name = $v;

        return $this;
    }
    public function locationId(int $v): self
    {
        $this->locationId = $v;

        return $this;
    }
    public function fqdn(string $v): self
    {
        $this->fqdn = $v;

        return $this;
    }
    public function scheme(string $v): self
    {
        $this->scheme = $v;

        return $this;
    }
    public function behindProxy(bool $v): self
    {
        $this->behindProxy = $v;

        return $this;
    }
    public function maintenance(bool $v): self
    {
        $this->maintenance = $v;

        return $this;
    }
    public function memory(int $v): self
    {
        $this->memory = $v;

        return $this;
    }
    public function memoryOver(int $v): self
    {
        $this->memoryOverallocate = $v;

        return $this;
    }
    public function disk(int $v): self
    {
        $this->disk = $v;

        return $this;
    }
    public function diskOver(int $v): self
    {
        $this->diskOverallocate = $v;

        return $this;
    }
    public function uploadSize(int $v): self
    {
        $this->uploadSize = $v;

        return $this;
    }
    public function daemon(int $listen, int $sftp): self
    {
        $this->daemonListen = $listen;
        $this->daemonSftp   = $sftp;

        return $this;
    }
    public function description(?string $text): self
    {
        $this->description = $text ?: null;

        return $this;
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        $m   = [];
        $map = [
            'name'                => $this->name,
            'location_id'         => $this->locationId,
            'fqdn'                => $this->fqdn,
            'scheme'              => $this->scheme,
            'behind_proxy'        => $this->behindProxy,
            'maintenance_mode'    => $this->maintenance,
            'memory'              => $this->memory,
            'memory_overallocate' => $this->memoryOverallocate,
            'disk'                => $this->disk,
            'disk_overallocate'   => $this->diskOverallocate,
            'upload_size'         => $this->uploadSize,
            'daemon_listen'       => $this->daemonListen,
            'daemon_sftp'         => $this->daemonSftp,
            'description'         => $this->description,
        ];
        foreach ($map as $k => $v) {
            if ($v !== null) {
                $m[$k] = $v;
            }
        }

        return $m;
    }
}
