<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Client;
use App\Repositories\Contracts\ClientRepositoryInterface;

final class ClientRepository implements ClientRepositoryInterface
{
    public function findById(int $id): ?Client
    {
        return Client::query()->find($id);
    }

    public function findByUcn(string $ucn): ?Client
    {
        return Client::query()->where('ucn', $ucn)->first();
    }

    public function create(array $attributes): Client
    {
        return Client::query()->create($attributes);
    }

    public function update(Client $client, array $attributes): Client
    {
        $client->update($attributes);

        return $client->refresh();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function upsertByUcn(string $ucn, array $attributes): Client
    {
        return Client::query()->updateOrCreate(['ucn' => $ucn], $attributes);
    }
}
