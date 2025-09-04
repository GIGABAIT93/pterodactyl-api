<?php

declare(strict_types=1);

namespace Gigabait93\Support\Builders;

use Gigabait93\Support\Responses\BaseResponse;

class ListBuilder extends GetBuilder
{
    protected bool $fetchAll = false;

    public function perPage(int $size): self
    {
        $this->params['per_page'] = max(1, $size);

        return $this;
    }

    public function page(int $number): self
    {
        $this->params['page'] = $number;

        return $this;
    }

    public function allPages(): self
    {
        $this->fetchAll = true;

        return $this;
    }

    public function send(): BaseResponse
    {
        $class = $this->responseClass;

        if ($this->fetchAll) {
            $items = $this->client->allPages($this->path, $this->params);
            $first = $this->client->requestResponse('GET', $this->path, $this->params + ['page' => 1]);

            if (method_exists($class, 'fromBase')) {
                /** @var BaseResponse $resp */
                $resp = $class::fromBase($first);
                // override with aggregated items into data only
                $resp->data = $items;

                return $resp;
            }

            return new $class(true, $first->status, $first->headers, $items, null, $first->raw, $first->meta, $first->payload);
        }

        return parent::send();
    }
}
