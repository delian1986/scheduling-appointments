<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->text('description')->nullable();
            $table->dateTime('scheduled_at');
            $table->string('notification_method', 30);
            $table->string('notification_status', 30)->default('pending');
            $table->dateTime('notified_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('scheduled_at', 'scheduled_at_index');
            $table->index(['client_id', 'scheduled_at'], 'client_scheduled_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
