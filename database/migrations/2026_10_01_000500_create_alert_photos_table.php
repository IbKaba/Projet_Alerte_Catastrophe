<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alert_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->unsignedTinyInteger('position')->default(1);
            $table->unique(['alert_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_photos');
    }
};
