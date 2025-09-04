<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Users\Params;

final class UserCreateParams
{
    private string $email;
    private string $username;
    private string $firstName;
    private string $lastName;

    private ?string $password   = null;
    private ?string $externalId = null;
    private ?string $language   = null;
    private ?bool $rootAdmin    = null;

    public function __construct(string $email, string $username, string $firstName, string $lastName)
    {
        $this->email     = $email;
        $this->username  = $username;
        $this->firstName = $firstName;
        $this->lastName  = $lastName;
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

    /**
     * @return array{
     *   email:string,username:string,first_name:string,last_name:string,
     *   password?:string,external_id?:string,language?:string,root_admin?:bool
     * }
     */
    public function toArray(): array
    {
        $out = [
            'email'      => $this->email,
            'username'   => $this->username,
            'first_name' => $this->firstName,
            'last_name'  => $this->lastName,
        ];
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
