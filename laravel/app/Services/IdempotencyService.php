<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Contracts\Cache\Factory as CacheFactory;

final class IdempotencyService
{
    public function __construct(
        private readonly CacheFactory $cache,
    ) {}

    public function acquire(string $key, int $ttlSeconds = 604_800): bool
    {
        return $this->cache->store()->add($key, 1, $ttlSeconds);
    }

    public function release(string $key): void
    {
        $this->cache->store()->forget($key);
    }
}
