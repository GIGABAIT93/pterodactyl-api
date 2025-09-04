<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Nodes\Params;

final class NodeCreateParams
{
    // Minimal commonly used fields
    private string $name;
    private int $locationId;
    private string $fqdn;
    private string $scheme    = 'https';
    private bool $behindProxy = true;
    private bool $maintenance = false;

    private int $memory;              // MB
    private int $memoryOverallocate;  // percent (-1 unlimited)
    private int $disk;                // MB
    private int $diskOverallocate;    // percent (-1 unlimited)
    private int $uploadSize      = 100;    // MB
    private int $daemonListen    = 8080;
    private int $daemonSftp      = 2022;
    private ?string $description = null;

    public function __construct(
        string $name,
        int $locationId,
        string $fqdn,
        int $memory,
        int $memoryOver,
        int $disk,
        int $diskOver
    ) {
        $this->name               = $name;
        $this->locationId         = $locationId;
        $this->fqdn               = $fqdn;
        $this->memory             = $memory;
        $this->memoryOverallocate = $memoryOver;
        $this->disk               = $disk;
        $this->diskOverallocate   = $diskOver;
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
    public function uploadSize(int $mb): self
    {
        $this->uploadSize = $mb;

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
        $out = [
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
        ];
        if ($this->description !== null) {
            $out['description'] = $this->description;
        }

        return $out;
    }
}
