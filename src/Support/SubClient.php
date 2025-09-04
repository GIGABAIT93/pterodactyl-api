<?php

namespace Gigabait93\Support;

use Gigabait93\Pterodactyl;
use Gigabait93\Support\Responses\BaseResponse;

abstract class SubClient
{
    protected string $endpoint;

    public function __construct(protected Pterodactyl $ptero, string $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function ptero(): Pterodactyl
    {
        return $this->ptero;
    }

    /**
     * Low-level access: return raw HTTP response wrapped.
     */
    public function requestResponse(string $method, string $path = '', $data = null, ?string $tokenOverride = null): BaseResponse
    {
        $resp = $this->ptero->makeRawRequest($method, $this->endpoint . $path, $data, $tokenOverride);

        return BaseResponse::fromHttp($resp);
    }

    protected function request(string $method, string $path = '', $data = null, ?string $tokenOverride = null): mixed
    {
        return $this->ptero->makeRequest($method, $this->endpoint . $path, $data, $tokenOverride);
    }

    protected function get(string $path = '', array $query = [], ?string $tokenOverride = null): mixed
    {
        return $this->request('GET', $path, $query, $tokenOverride);
    }

    protected function post(string $path = '', array|string|null $data = null, ?string $tokenOverride = null): mixed
    {
        return $this->request('POST', $path, $data, $tokenOverride);
    }

    protected function patch(string $path = '', array|string|null $data = null, ?string $tokenOverride = null): mixed
    {
        return $this->request('PATCH', $path, $data, $tokenOverride);
    }

    protected function delete(string $path = '', array|string|null $data = null, ?string $tokenOverride = null): mixed
    {
        return $this->request('DELETE', $path, $data, $tokenOverride);
    }

    /**
     * Fetch all pages of a paginated endpoint and merge items under given key.
     * Assumes Pterodactyl meta.pagination structure.
     *
     * @return array<int,mixed>
     */
    public function allPages(string $path = '', array $params = [], string $dataKey = 'data'): array
    {
        $page  = 1;
        $items = [];

        do {
            $resp  = $this->get($path, array_merge($params, ['page' => $page]));
            $chunk = is_array($resp) ? ($resp[$dataKey] ?? []) : [];
            if (!is_array($chunk)) {
                $chunk = [];
            }
            $items = array_merge($items, $chunk);

            $meta  = is_array($resp) ? ($resp['meta']['pagination'] ?? null) : null;
            $cur   = is_array($meta) ? (int)($meta['current_page'] ?? 0) : 0;
            $total = is_array($meta) ? (int)($meta['total_pages'] ?? 0) : 0;
            $page++;
        } while ($cur > 0 && $total > 0 && $cur < $total);

        return $items;
    }
}
