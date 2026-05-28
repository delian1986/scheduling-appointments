<?php

declare(strict_types=1);

namespace App\Repositories\Cache;

use App\Models\Appointment;
use App\Models\Client;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as LengthAwarePaginatorInstance;
use Illuminate\Pagination\Paginator;

final class CachingAppointmentRepository implements AppointmentRepositoryInterface
{
    private const TTL = 3600;

    public function __construct(
        private readonly AppointmentRepositoryInterface $inner,
        private readonly CacheRepository $cache,
        private readonly AppointmentCacheManager $appointmentCacheManager,
    ) {}

    public function findById(int $id): ?Appointment
    {
        $cached = $this->cache->tags([AppointmentCacheManager::TAG])->remember(
            "appointments:details:{$id}",
            self::TTL,
            fn (): ?array => $this->inner->findById($id)?->toArray(),
        );

        return $this->hydrateAppointment($cached);
    }

    /**
     * @param  array<string, string>  $filters
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $key = $this->listCacheKey($filters, $perPage);

        $cached = $this->cache->tags([AppointmentCacheManager::TAG])->remember(
            $key,
            self::TTL,
            fn (): array => $this->serializePaginator($this->inner->paginate($filters, $perPage)),
        );

        return $this->hydratePaginator($cached);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Appointment
    {
        $appointment = $this->inner->create($attributes);

        $this->flushCache();

        return $appointment;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Appointment $appointment, array $attributes): Appointment
    {
        $appointment = $this->inner->update($appointment, $attributes);

        $this->flushCache();

        return $appointment;
    }

    public function delete(Appointment $appointment): bool
    {
        $deleted = $this->inner->delete($appointment);

        $this->flushCache();

        return $deleted;
    }

    /**
     * @param  array<string, string>  $filters
     */
    private function listCacheKey(array $filters, int $perPage): string
    {
        ksort($filters);

        return 'appointments:list:'.http_build_query([
            ...$filters,
            'per_page' => $perPage,
            'page' => Paginator::resolveCurrentPage(),
        ]);
    }

    private function flushCache(): void
    {
        $this->appointmentCacheManager->flushAll();
    }

    /**
     * @return array<string, mixed>
     */
    private function serializePaginator(LengthAwarePaginatorInstance $paginator): array
    {
        return [
            'total' => $paginator->total(),
            'perPage' => $paginator->perPage(),
            'currentPage' => $paginator->currentPage(),
            'path' => $paginator->path(),
            'query' => $paginator->getOptions()['query'] ?? [],
            'items' => collect($paginator->items())
                ->map(function (Appointment $appointment): array {
                    $attributes = $appointment->toArray();

                    if ($appointment->relationLoaded('client') && $appointment->client !== null) {
                        $attributes['client'] = $appointment->client->toArray();
                    }

                    return $attributes;
                })
                ->values()
                ->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $cached
     */
    private function hydratePaginator(array $cached): LengthAwarePaginator
    {
        $items = collect($cached['items'])
            ->map(fn (array $attributes): Appointment => $this->hydrateAppointmentWithClient($attributes));

        return new LengthAwarePaginatorInstance(
            $items,
            $cached['total'],
            $cached['perPage'],
            $cached['currentPage'],
            [
                'path' => $cached['path'],
                'query' => $cached['query'],
            ],
        );
    }

    /**
     * @param  array<string, mixed>|null  $attributes
     */
    private function hydrateAppointment(?array $attributes): ?Appointment
    {
        if ($attributes === null) {
            return null;
        }

        return $this->hydrateAppointmentWithClient($attributes);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function hydrateAppointmentWithClient(array $attributes): Appointment
    {
        $clientAttributes = $attributes['client'] ?? null;
        unset($attributes['client']);

        $appointment = (new Appointment)->newFromBuilder($attributes);

        if (is_array($clientAttributes)) {
            $appointment->setRelation('client', (new Client)->newFromBuilder($clientAttributes));
        }

        return $appointment;
    }
}
