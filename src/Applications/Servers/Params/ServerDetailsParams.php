<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Servers\Params;

/**
 * DTO for PATCH /servers/{id}/details
 * Short and clear: only include fields you set.
 */
final class ServerDetailsParams
{
    private ?string $name         = null;
    private ?int $user            = null;
    private ?string $externalId   = null;
    private bool $externalIdIsSet = false;
    private ?string $description  = null;

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function user(int $userId): self
    {
        $this->user = $userId;

        return $this;
    }

    public function externalId(?string $id): self
    {
        $this->externalId      = $id ?: null;
        $this->externalIdIsSet = true;

        return $this;
    }

    public function description(?string $text): self
    {
        $this->description = $text ?: null;

        return $this;
    }

    /**
     * @return array{name?:string,user?:int,external_id?:?string,description?:?string}
     */
    public function toArray(): array
    {
        $out = [];
        if ($this->name !== null) {
            $out['name'] = $this->name;
        }
        if ($this->user !== null) {
            $out['user'] = $this->user;
        }
        if ($this->externalIdIsSet) {
            $out['external_id'] = $this->externalId;
        } // allow explicit null to clear
        if ($this->description !== null) {
            $out['description'] = $this->description;
        }

        return $out;
    }
}
