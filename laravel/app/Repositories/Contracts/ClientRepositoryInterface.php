<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Client;

interface ClientRepositoryInterface
{
    public function findById(int $id): ?Client;

    public function findByUcn(string $ucn): ?Client;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Client;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Client $client, array $attributes): Client;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function upsertByUcn(string $ucn, array $attributes): Client;
}
