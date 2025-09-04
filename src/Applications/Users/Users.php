<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Users;

use Gigabait93\Applications\Users\Builders\UsersItemBuilder;
use Gigabait93\Applications\Users\Builders\UsersListBuilder;
use Gigabait93\Applications\Users\Params\UserCreateParams;
use Gigabait93\Applications\Users\Params\UserUpdateParams;
use Gigabait93\Applications\Users\Responses\UsersListResponse;
use Gigabait93\Pterodactyl;
use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\Responses\ItemResponse;
use Gigabait93\Support\SubClient;

class Users extends SubClient
{
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/application/users');
    }

    public function list(): UsersListBuilder
    {
        return new UsersListBuilder($this, '', UsersListResponse::class);
    }

    public function show(int $id): UsersItemBuilder
    {
        return new UsersItemBuilder($this, $id);
    }

    public function external(string $externalId): UsersItemBuilder
    {
        return new UsersItemBuilder($this, 'external/' . $externalId);
    }

    public function create(UserCreateParams $params): ItemResponse
    {
        $r = $this->requestResponse('POST', '', $params->toArray());

        return ItemResponse::fromBase($r);
    }

    public function update(int $id, UserUpdateParams $params): ItemResponse
    {
        $r = $this->requestResponse('PATCH', '/' . $id, $params->toArray());

        return ItemResponse::fromBase($r);
    }

    public function destroy(int $id): ActionResponse
    {
        $r = $this->requestResponse('DELETE', '/' . $id);

        return ActionResponse::fromBase($r);
    }

}
