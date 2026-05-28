<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NotificationMethod;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['full_name', 'ucn', 'email', 'phone'])]
final class Client extends Model
{
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function contactFor(NotificationMethod $method): ?string
    {
        return match ($method) {
            NotificationMethod::Email => $this->email,
            NotificationMethod::Sms => $this->phone,
        };
    }
}
