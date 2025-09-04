<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Servers\Params;

/**
 * DTO for PATCH /servers/{id}/startup
 */
final class ServerStartupParams
{
    private ?int $egg        = null;
    private ?string $startup = null;
    private ?string $image   = null;
    /** @var array<string,string> */
    private array $env         = [];
    private ?bool $skipScripts = null;

    /** @var array<string,string>|null */
    private ?array $dockerImages = null; // optional: map label=>image

    public function egg(int $eggId): self
    {
        $this->egg = $eggId;

        return $this;
    }
    public function startup(string $cmd): self
    {
        $this->startup = $cmd;

        return $this;
    }
    public function image(string $dockerImage): self
    {
        $this->image = $dockerImage;

        return $this;
    }
    public function skipScripts(bool $skip = true): self
    {
        $this->skipScripts = $skip;

        return $this;
    }

    /** @param array<string,scalar> $vars */
    public function env(array $vars): self
    {
        foreach ($vars as $k => $v) {
            $this->env[(string)$k] = is_bool($v) ? ($v ? '1' : '0') : (string)$v;
        }

        return $this;
    }

    /** @param array<string,string> $images */
    public function dockerImages(array $images): self
    {
        $this->dockerImages = $images;

        return $this;
    }

    /**
     * @return array{
     *   egg?:int,startup?:string,image?:string,skip_scripts?:bool,
     *   environment?:array<string,string>,docker_images?:array<string,string>
     * }
     */
    public function toArray(): array
    {
        $out = [];
        if ($this->egg !== null) {
            $out['egg'] = $this->egg;
        }
        if ($this->startup !== null) {
            $out['startup'] = $this->startup;
        }
        if ($this->image !== null) {
            $out['image'] = $this->image;
        }
        if ($this->skipScripts !== null) {
            $out['skip_scripts'] = $this->skipScripts;
        }
        if ($this->env !== []) {
            $out['environment'] = $this->env;
        }
        if ($this->dockerImages !== null) {
            $out['docker_images'] = $this->dockerImages;
        }

        return $out;
    }
}
