<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->unique()->constrained()->onDelete('cascade');
            $table->timestamp('checked_in_at');
            $table->foreignId('checked_in_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
