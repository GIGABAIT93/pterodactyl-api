<?php

declare(strict_types=1);

namespace Gigabait93;

use Exception;
use Gigabait93\Applications\Allocations\Allocations;
use Gigabait93\Applications\Eggs\Eggs;
use Gigabait93\Applications\Locations\Locations;
use Gigabait93\Applications\Nests\Nests;
use Gigabait93\Applications\Nodes\Nodes;
use Gigabait93\Applications\Servers\Servers;
use Gigabait93\Applications\Users\Users;
use Gigabait93\Client\Backups\Backups;
use Gigabait93\Client\Database\Database;
use Gigabait93\Client\Files\Files;
use Gigabait93\Client\Network\Network;
use Gigabait93\Client\Schedules\Schedules;
use Gigabait93\Client\Server\Server;
use Gigabait93\Client\Settings\Settings;
use Gigabait93\Client\Startup\Startup;
use Gigabait93\Client\Subusers\Subusers;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as HttpResponse;

/**
 * Main SDK entry point.
 *
 * @property-read Servers $servers
 * @property-read Locations $locations
 * @property-read Allocations $allocations
 * @property-read Users $users
 * @property-read Nests $nests
 * @property-read Eggs $eggs
 * @property-read Nodes $nodes
 * @property-read Server $server
 * @property-read Files $files
 * @property-read Network $network
 * @property-read Database $database
 * @property-read Schedules $schedules
 * @property-read Backups $backups
 * @property-read Startup $startup
 * @property-read Settings $settings
 * @property-read Subusers $subusers
 */
class Pterodactyl
{
    // --- Sub-clients (public for direct access; initialized in constructor)
    public Servers $servers;
    public Locations $locations;
    public Allocations $allocations;
    public Users $users;
    public Nests $nests;
    public Eggs $eggs;
    public Nodes $nodes;

    public Server $server;
    public Files $files;
    public Network $network;
    public Database $database;
    public Schedules $schedules;
    public Backups $backups;
    public Startup $startup;
    public Settings $settings;
    public Subusers $subusers;

    // --- HTTP/config
    protected string $apiKey;
    protected string $baseUrl;
    protected HttpClient $http;

    /**
     * Create a configured instance (recommended).
     */
    public static function make(string $baseUrl, string $clientAdminApiKey, int $timeout = 30): self
    {
        return new self($clientAdminApiKey, $baseUrl, $timeout);
    }

    /**
     * @param string $clientAdminKey Client API token with admin privileges
     * @param string $baseUrl Panel base URL (e.g. https://panel.example.com)
     * @param int $timeout HTTP timeout in seconds
     */
    public function __construct(string $clientAdminKey, string $baseUrl, int $timeout = 30)
    {
        $this->ensureValidToken($clientAdminKey);
        $this->apiKey  = $clientAdminKey;
        $this->baseUrl = rtrim($baseUrl, '/');

        $this->http = new HttpClient([
            'base_uri'    => $this->baseUrl . '/',
            'http_errors' => false,
            'timeout'     => max(1, $timeout),
        ]);

        // Applications
        $this->servers     = new Servers($this);
        $this->locations   = new Locations($this);
        $this->allocations = new Allocations($this);
        $this->users       = new Users($this);
        $this->nests       = new Nests($this);
        $this->eggs        = new Eggs($this);
        $this->nodes       = new Nodes($this);

        // Client
        $this->server    = new Server($this);
        $this->files     = new Files($this);
        $this->database  = new Database($this);
        $this->schedules = new Schedules($this);
        $this->network   = new Network($this);
        $this->backups   = new Backups($this);
        $this->startup   = new Startup($this);
        $this->settings  = new Settings($this);
        $this->subusers  = new Subusers($this);
    }

    // ---------------------------------------------------------------------
    // Configuration setters
    // ---------------------------------------------------------------------

    /**
     * Set/replace Client Admin API token (used for all endpoints).
     */
    public function setNewApiKey(string $token): void
    {
        $this->ensureValidToken($token);
        $this->apiKey = $token;
    }

    /**
     * Change HTTP timeout.
     */
    public function setTimeout(int $seconds): void
    {
        $this->http = new HttpClient([
            'base_uri'    => $this->baseUrl . '/',
            'http_errors' => false,
            'timeout'     => max(1, $seconds),
        ]);
    }

    /**
     * Inject a custom Guzzle client (useful for testing/mocking).
     */
    public function setHttpClient(HttpClient $client): void
    {
        $this->http = $client;
    }

    // ---------------------------------------------------------------------
    // Request helpers
    // ---------------------------------------------------------------------

    /**
     * Perform HTTP request and decode JSON automatically.
     * Returns decoded array for JSON responses; otherwise returns raw body string.
     * On transport errors returns array with 'error' message.
     *
     * @param string $method GET|POST|PUT|DELETE|PATCH
     * @param string $url Relative path (e.g. 'api/client/servers/...') or absolute
     * @param array|string|null $data Query (GET) or JSON/plain body (others)
     * @param string|null $tokenOverride
     * @return array|string
     */
    public function makeRequest(string $method, string $url, array|string|null $data = null, ?string $tokenOverride = null): array|string
    {
        if ($tokenOverride !== null) {
            $this->ensureValidToken($tokenOverride);
        }
        $token   = $tokenOverride ?? $this->apiKey;
        $method  = strtolower($method);
        $allowed = ['get', 'post', 'put', 'delete', 'patch'];

        if (!in_array($method, $allowed, true)) {
            throw new InvalidArgumentException('Invalid HTTP method.');
        }

        $options = $this->buildOptions($token, $method, $data);

        try {
            $resp   = $this->http->request(strtoupper($method), ltrim($url, '/'), $options);
            $status = $resp->getStatusCode();
            $ctype  = $resp->getHeaderLine('Content-Type');
            $body   = (string)$resp->getBody();

            if (stripos($ctype, 'application/json') !== false) {
                $decoded = json_decode($body, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decoded;
                }
            }

            if ($status >= 200 && $status < 300) {
                return $body;
            }

            return [
                'error'  => 'Request failed with status code ' . $status,
                'body'   => $body,
                'status' => $status,
            ];
        } catch (GuzzleException $e) {
            return ['error' => 'HTTP exception: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['error' => 'Exception occurred: ' . $e->getMessage()];
        }
    }

    /**
     * Perform HTTP request and return raw PSR-7 response.
     */
    public function makeRawRequest(string $method, string $url, array|string|null $data = null, ?string $tokenOverride = null): HttpResponse
    {
        if ($tokenOverride !== null) {
            $this->ensureValidToken($tokenOverride);
        }
        $token   = $tokenOverride ?? $this->apiKey;
        $method  = strtolower($method);
        $options = $this->buildOptions($token, $method, $data);

        return $this->http->request(strtoupper($method), ltrim($url, '/'), $options);
    }

    // ---------------------------------------------------------------------
    // Internals
    // ---------------------------------------------------------------------

    // No token resolution by path anymore; a single clientAdminKey is used.

    /**
     * Build Guzzle options for request based on method/data.
     *
     * @param string $token
     * @param string $method lowercased
     * @param array|string|null $data
     * @return array<string,mixed>
     */
    protected function buildOptions(string $token, string $method, array|string|null $data): array
    {
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];

        $options = ['headers' => $headers];

        if ($method === 'get') {
            if (is_array($data)) {
                $options['query'] = $data;
            }

            return $options;
        }

        if (is_string($data)) {
            // raw body (used e.g. by some file APIs)
            $options['body']                    = $data;
            $options['headers']['Content-Type'] = 'text/plain';
        } elseif (is_array($data)) {
            // JSON body (default)
            $options['json'] = $data;
        }

        return $options;
    }

    /**
     * Ensure provided token meets basic format requirements.
     * Requires panel tokens starting with 'ptlc_'.
     */
    private function ensureValidToken(string $token): void
    {
        // basic sanity check
        if ($token === '') {
            throw new InvalidArgumentException('API key must not be empty');
        }

        $supportedPrefixes = ['ptlc_', 'pacc_'];

        foreach ($supportedPrefixes as $prefix) {
            if (str_starts_with($token, $prefix)) {
                // prefix is valid for Pterodactyl or Pelican
                return;
            }
        }

        throw new InvalidArgumentException(sprintf(
            'API key must start with one of: %s',
            implode(', ', $supportedPrefixes)
        ));
    }
}
