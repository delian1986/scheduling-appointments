<?php

declare(strict_types=1);

namespace App\Repositories\Cache;

use Illuminate\Contracts\Cache\Factory as CacheFactory;

final class AppointmentCacheManager
{
    public const TAG = 'appointments';

    public function __construct(
        private readonly CacheFactory $cache,
    ) {}

    public function flushAll(): void
    {
        $this->cache->store()->tags([self::TAG])->flush();
    }
}
