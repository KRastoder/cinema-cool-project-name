<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('show_seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('show_id')->constrained()->onDelete('cascade');
            $table->foreignId('seat_id')->constrained()->onDelete('cascade');
            $table->foreignId('reservation_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['available', 'held', 'sold'])->default('available');
            $table->timestamp('held_at')->nullable();

            // CRITICAL: prevents double booking
            $table->unique(['show_id', 'seat_id']);

            // For expiration job performance
            $table->index(['status', 'held_at']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('show_seats');
    }
};
