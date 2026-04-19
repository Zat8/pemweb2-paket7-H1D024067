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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_category_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('speaker')->nullable();
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('location');
            $table->unsignedInteger('quota');
            $table->string('poster')->nullable();
            $table->string('certificate_template')->nullable();
            $table->enum('status', ['draft', 'published', 'closed', 'finished'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
