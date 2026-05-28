<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Redis;

final class IdempotencyService
{
    public function acquire(string $key, int $ttlSeconds = 604_800): bool
    {
        return (bool) Redis::set($key, 1, 'EX', $ttlSeconds, 'NX');
    }

    public function release(string $key): void
    {
        Redis::del($key);
    }
}
