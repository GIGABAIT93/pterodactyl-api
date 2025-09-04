<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Servers\Params;

use InvalidArgumentException;

/**
 * DTO параметрів для створення нового сервера (POST /api/application/servers).
 *
 * Приклад:
 * $params = (new CreateServerParams(
 *     name: 'My MC',
 *     userId: 42,
 *     eggId: 15,
 *     dockerImage: 'ghcr.io/pterodactyl/yolks:java_17',
 *     startup: 'java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}'
 * ))
 *     ->setLimits(memory: 4096, swap: 0, disk: 20000, io: 500, cpu: 200)
 *     ->setFeatureLimits(databases: 2, allocations: 2, backups: 5)
 *     ->env('SERVER_JARFILE', 'server.jar')
 *     ->env('MOTD', 'Hello')
 *     ->useAllocation(defaultAllocationId: 1234, additionalIds: [1235])
 *     ->description('Production server')
 *     ->startOnCompletion(true)
 *     ->skipScripts(false)
 *     ->oomDisabled(false);
 *
 * $payload = $params->toArray();
 */
final class CreateServerParams
{
    // Обов'язкові
    private string $name;
    private int $userId;
    private int $eggId;
    private string $dockerImage;
    private string $startup;

    // Опційні метадані
    private ?string $description = null;
    private ?string $externalId  = null;

    // Ліміти
    /** @var array{memory:int,swap:int,disk:int,io:int,cpu:int,threads:?string} */
    private array $limits = [
        'memory'  => 0,
        'swap'    => 0,
        'disk'    => 0,
        'io'      => 500,
        'cpu'     => 0,
        'threads' => null,
    ];

    /** @var array{databases:int,allocations:int,backups:int} */
    private array $featureLimits = [
        'databases'   => 0,
        'allocations' => 1,
        'backups'     => 0,
    ];

    /** @var array<string,scalar> */
    private array $environment = [];

    // Розміщення: або allocation, або deploy (взаємовиключні)
    /** @var null|array{default:int,additional?:int[]} */
    private ?array $allocation = null;

    /** @var null|array{locations:int[],dedicated_ip:bool,port_range:string[]} */
    private ?array $deploy = null;

    // Прапори поведінки
    private bool $skipScripts       = false;
    private bool $startOnCompletion = true;
    private bool $oomDisabled       = false;

    /**
     * @param string $name Назва сервера (відображувана)
     * @param int $userId ID користувача (панелі), власника сервера
     * @param int $eggId ID яйця (egg)
     * @param string $dockerImage Docker image (один зі списку egg або кастомний)
     * @param string $startup Команда запуску
     */
    public function __construct(
        string $name,
        int    $userId,
        int    $eggId,
        string $dockerImage,
        string $startup
    ) {
        $this->name        = $name;
        $this->userId      = $userId;
        $this->eggId       = $eggId;
        $this->dockerImage = $dockerImage;
        $this->startup     = $startup;
    }

    // -------- Метадані

    public function description(?string $description): self
    {
        $this->description = $description ?: null;

        return $this;
    }

    public function externalId(?string $externalId): self
    {
        $this->externalId = $externalId ?: null;

        return $this;
    }

    // -------- Ліміти

    /**
     * Встановити ресурси. Пояснення:
     * - memory (MB), 0/унліміт не дозволено — зазвичай >0
     * - swap (MB): -1 = без обмежень, 0 = заборонено
     * - disk (MB)
     * - io (від 10 до 1000, дефолт 500)
     * - cpu (%*100). 0 = без ліміту, 100 = 1 CPU, 200 = 2 CPU і т.д.
     * - threads (рядок CPU pinning, напр. "0-1,3")
     */
    public function setLimits(
        int     $memory,
        int     $swap,
        int     $disk,
        int     $io = 500,
        int     $cpu = 0,
        ?string $threads = null
    ): self {
        if ($memory < 1) {
            throw new InvalidArgumentException('limits.memory must be >= 1 MB');
        }
        if ($disk < 1) {
            throw new InvalidArgumentException('limits.disk must be >= 1 MB');
        }
        if ($io < 10 || $io > 1000) {
            throw new InvalidArgumentException('limits.io must be between 10 and 1000');
        }
        if ($cpu < 0) {
            throw new InvalidArgumentException('limits.cpu must be >= 0');
        }

        $this->limits = [
            'memory'  => $memory,
            'swap'    => $swap,
            'disk'    => $disk,
            'io'      => $io,
            'cpu'     => $cpu,
            'threads' => $threads ?: null,
        ];

        return $this;
    }

    public function setFeatureLimits(int $databases = 0, int $allocations = 1, int $backups = 0): self
    {
        foreach (['databases' => $databases, 'allocations' => $allocations, 'backups' => $backups] as $k => $v) {
            if ($v < 0) {
                throw new InvalidArgumentException("feature_limits.$k must be >= 0");
            }
        }

        $this->featureLimits = [
            'databases'   => $databases,
            'allocations' => $allocations,
            'backups'     => $backups,
        ];

        return $this;
    }

    // -------- Оточення

    /**
     * Додати/змінити змінну оточення (усі значення на виході будуть рядками).
     * @param scalar $value
     */
    public function env(string $key, float|bool|int|string $value): self
    {
        $this->environment[$key] = $value;

        return $this;
    }

    /**
     * Повністю замінити оточення.
     * @param array<string,scalar> $env
     */
    public function setEnvironment(array $env): self
    {
        foreach ($env as $k => $v) {
            if (!is_string($k) || $k === '' || !is_scalar($v)) {
                throw new InvalidArgumentException('Environment must be array<string, scalar>');
            }
        }
        $this->environment = $env;

        return $this;
    }

    // -------- Розміщення (один з двох варіантів)

    /**
     * Використати вже створену алокацію(ї).
     * @param int $defaultAllocationId ID основної алокації
     * @param int[] $additionalIds Додаткові allocation IDs (може бути порожнім)
     */
    public function useAllocation(int $defaultAllocationId, array $additionalIds = []): self
    {
        if ($defaultAllocationId <= 0) {
            throw new InvalidArgumentException('allocation.default must be > 0');
        }
        foreach ($additionalIds as $id) {
            if (!is_int($id) || $id <= 0) {
                throw new InvalidArgumentException('allocation.additional must be array<int> of positive IDs');
            }
        }
        $this->allocation = [
            'default' => $defaultAllocationId,
        ];
        if ($additionalIds !== []) {
            $this->allocation['additional'] = array_values(array_unique($additionalIds));
        }
        $this->deploy = null; // взаємовиключно

        return $this;
    }

    /**
     * Автодеплой: панель сама виділить алокацію за критеріями.
     * @param int[] $locationIds Локації для пошуку
     * @param bool $dedicatedIp Чи вимагати виділену IP
     * @param string[] $portRanges Масив рядків "7000-7090" або "25565"
     */
    public function useDeploy(array $locationIds, bool $dedicatedIp = false, array $portRanges = []): self
    {
        if ($locationIds === []) {
            throw new InvalidArgumentException('deploy.locations must not be empty');
        }
        foreach ($locationIds as $id) {
            if (!is_int($id) || $id <= 0) {
                throw new InvalidArgumentException('deploy.locations must be array<int> of positive IDs');
            }
        }
        foreach ($portRanges as $range) {
            if (!is_string($range) || $range === '') {
                throw new InvalidArgumentException('deploy.port_range must be array<string>');
            }
        }

        $this->deploy = [
            'locations'    => array_values(array_unique($locationIds)),
            'dedicated_ip' => $dedicatedIp,
            'port_range'   => array_values($portRanges),
        ];
        $this->allocation = null; // взаємовиключно

        return $this;
    }

    // -------- Прапори

    public function skipScripts(bool $skip = true): self
    {
        $this->skipScripts = $skip;

        return $this;
    }

    public function startOnCompletion(bool $start = true): self
    {
        $this->startOnCompletion = $start;

        return $this;
    }

    public function oomDisabled(bool $disabled = true): self
    {
        $this->oomDisabled = $disabled;

        return $this;
    }

    // -------- Вивід

    /**
     * Зібрати payload для API.
     * @return array{
     *   name:string,
     *   user:int,
     *   egg:int,
     *   docker_image:string,
     *   startup:string,
     *   environment:array<string,string>,
     *   limits:array{memory:int,swap:int,disk:int,io:int,cpu:int,threads?:string},
     *   feature_limits:array{databases:int,allocations:int,backups:int},
     *   allocation?:array{default:int,additional?:int[]},
     *   deploy?:array{locations:int[],dedicated_ip:bool,port_range:string[]},
     *   description?:string,
     *   external_id?:string,
     *   skip_scripts:bool,
     *   start_on_completion:bool,
     *   oom_disabled:bool
     * }
     */
    public function toArray(): array
    {
        // Перевірка взаємовиключності розміщення
        if ($this->allocation === null && $this->deploy === null) {
            throw new InvalidArgumentException('Either allocation or deploy must be provided');
        }
        if ($this->allocation !== null && $this->deploy !== null) {
            throw new InvalidArgumentException('allocation and deploy are mutually exclusive');
        }

        $limits = $this->limits;
        if ($limits['threads'] === null) {
            unset($limits['threads']);
        }

        $out = [
            'name'                => $this->name,
            'user'                => $this->userId,
            'egg'                 => $this->eggId,
            'docker_image'        => $this->dockerImage,
            'startup'             => $this->startup,
            'environment'         => $this->normalizeEnv($this->environment),
            'limits'              => $limits,
            'feature_limits'      => $this->featureLimits,
            'skip_scripts'        => $this->skipScripts,
            'start_on_completion' => $this->startOnCompletion,
            'oom_disabled'        => $this->oomDisabled,
        ];

        if ($this->allocation !== null) {
            $out['allocation'] = $this->allocation;
        }
        if ($this->deploy !== null) {
            $out['deploy'] = $this->deploy;
        }
        if ($this->description !== null) {
            $out['description'] = $this->description;
        }
        if ($this->externalId !== null) {
            $out['external_id'] = $this->externalId;
        }

        return $out;
    }

    /**
     * @param array<string,scalar> $env
     * @return array<string,string>
     */
    private function normalizeEnv(array $env): array
    {
        $result = [];
        foreach ($env as $k => $v) {
            if (is_bool($v)) {
                $result[$k] = $v ? '1' : '0';
            } else {
                $result[$k] = (string)$v;
            }
        }

        return $result;
    }
}
