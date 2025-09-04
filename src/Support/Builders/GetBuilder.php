<?php

namespace Gigabait93\Support\Builders;

use BackedEnum;
use Gigabait93\Support\Responses\BaseResponse;
use Gigabait93\Support\SubClient;

class GetBuilder
{
    protected array $params = [];

    public function __construct(
        protected SubClient $client,
        protected string    $path,
        protected string    $responseClass,
    ) {
    }

    /** Додати довільний query */
    public function param(string $key, mixed $value): self
    {
        $this->params[$key] = $value instanceof BackedEnum ? $value->value : $value;

        return $this;
    }

    /**
     * include=... (рядок/масив/enum)
     * @param string|array<int,string|BackedEnum>|BackedEnum $includes
     */
    public function includes(string|array|BackedEnum $includes): self
    {
        $list = [];
        if ($includes instanceof BackedEnum) {
            $list[] = $includes->value;
        } elseif (is_array($includes)) {
            foreach ($includes as $inc) {
                $list[] = $inc instanceof BackedEnum ? $inc->value : (string)$inc;
            }
        } else {
            $list[] = $includes;
        }

        $current                 = isset($this->params['include']) ? explode(',', (string)$this->params['include']) : [];
        $this->params['include'] = implode(',', array_unique(array_merge($current, $list)));

        return $this;
    }

    /** Виконати GET і зібрати відповідь потрібного класу */
    public function send(): BaseResponse
    {
        $r     = $this->client->requestResponse('GET', $this->path, $this->params);
        $class = $this->responseClass;

        if (method_exists($class, 'fromBase')) {
            return $class::fromBase($r);
        }

        return new $class($r->ok, $r->status, $r->headers, $r->data, $r->error, $r->raw, $r->meta, $r->payload);
    }
}
