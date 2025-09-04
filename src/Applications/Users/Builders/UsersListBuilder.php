<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Users\Builders;

use Gigabait93\Support\Builders\ListBuilder;

class UsersListBuilder extends ListBuilder
{
    private const SORTABLE = ['id','uuid','username','email','created_at','updated_at'];

    // Filters
    public function filterEmail(string $email): self
    {
        $this->params['filter']['email'] = $email;

        return $this;
    }
    public function filterUuid(string $uuid): self
    {
        $this->params['filter']['uuid'] = $uuid;

        return $this;
    }
    public function filterUsername(string $username): self
    {
        $this->params['filter']['username'] = $username;

        return $this;
    }
    public function filterExternalId(string $externalId): self
    {
        $this->params['filter']['external_id'] = $externalId;

        return $this;
    }

    // Sort: e.g., ->sort('username'), ->sort('created_at', true)
    public function sort(string $field, bool $desc = false): self
    {
        if (!in_array($field, self::SORTABLE, true)) {
            return $this; // ignore invalid fields silently
        }
        $this->params['sort'] = ($desc ? '-' : '') . $field;

        return $this;
    }
}
