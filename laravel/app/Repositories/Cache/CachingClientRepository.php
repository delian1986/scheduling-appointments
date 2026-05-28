<?php

declare(strict_types=1);

namespace App\Repositories\Cache;

use App\Models\Client;
use App\Repositories\Contracts\ClientRepositoryInterface;
use Illuminate\Cache\Repository as CacheRepository;

final class CachingClientRepository implements ClientRepositoryInterface
{
    private const TTL = 3600;

    public function __construct(
        private readonly ClientRepositoryInterface $inner,
        private readonly CacheRepository $cache,
        private readonly AppointmentCacheManager $appointmentCacheManager,
    ) {}

    public function findById(int $id): ?Client
    {
        return $this->inner->findById($id);
    }

    public function findByUcn(string $ucn): ?Client
    {
        $cached = $this->cache->tags([AppointmentCacheManager::TAG])->remember(
            "appointments:client:{$ucn}",
            self::TTL,
            fn (): ?array => $this->inner->findByUcn($ucn)?->toArray(),
        );

        return $cached === null ? null : (new Client)->newFromBuilder($cached);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Client
    {
        $client = $this->inner->create($attributes);

        $this->flushCache();

        return $client;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Client $client, array $attributes): Client
    {
        $client = $this->inner->update($client, $attributes);

        $this->flushCache();

        return $client;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function upsertByUcn(string $ucn, array $attributes): Client
    {
        $client = $this->inner->upsertByUcn($ucn, $attributes);

        $this->flushCache();

        return $client;
    }

    private function flushCache(): void
    {
        $this->appointmentCacheManager->flushAll();
    }
}
