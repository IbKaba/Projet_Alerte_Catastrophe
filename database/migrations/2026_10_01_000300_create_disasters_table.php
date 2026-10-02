<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disasters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('disaster_categories')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->unique(['category_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disasters');
    }
};
