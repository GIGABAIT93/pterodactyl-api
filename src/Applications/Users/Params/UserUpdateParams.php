<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Users\Params;

final class UserUpdateParams
{
    private ?string $email      = null;
    private ?string $username   = null;
    private ?string $firstName  = null;
    private ?string $lastName   = null;
    private ?string $password   = null;
    private ?string $externalId = null;
    private ?string $language   = null;
    private ?bool $rootAdmin    = null;

    public function email(string $email): self
    {
        $this->email = $email;

        return $this;
    }
    public function username(string $username): self
    {
        $this->username = $username;

        return $this;
    }
    public function firstName(string $first): self
    {
        $this->firstName = $first;

        return $this;
    }
    public function lastName(string $last): self
    {
        $this->lastName = $last;

        return $this;
    }
    public function password(?string $password): self
    {
        $this->password = $password ?: null;

        return $this;
    }
    public function externalId(?string $id): self
    {
        $this->externalId = $id ?: null;

        return $this;
    }
    public function language(?string $lang): self
    {
        $this->language = $lang ?: null;

        return $this;
    }
    public function rootAdmin(?bool $flag): self
    {
        $this->rootAdmin = $flag;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $out = [];
        if ($this->email !== null) {
            $out['email'] = $this->email;
        }
        if ($this->username !== null) {
            $out['username'] = $this->username;
        }
        if ($this->firstName !== null) {
            $out['first_name'] = $this->firstName;
        }
        if ($this->lastName !== null) {
            $out['last_name'] = $this->lastName;
        }
        if ($this->password !== null) {
            $out['password'] = $this->password;
        }
        if ($this->externalId !== null) {
            $out['external_id'] = $this->externalId;
        }
        if ($this->language !== null) {
            $out['language'] = $this->language;
        }
        if ($this->rootAdmin !== null) {
            $out['root_admin'] = $this->rootAdmin;
        }

        return $out;
    }
}
