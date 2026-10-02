<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('alerts', 'effective_severity')) {
            Schema::table('alerts', function (Blueprint $table): void {
                $table->dropColumn('effective_severity');
            });
        }

        if (Schema::hasColumn('disasters', 'default_severity')) {
            Schema::table('disasters', function (Blueprint $table): void {
                $table->dropColumn('default_severity');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('alerts', 'effective_severity')) {
            Schema::table('alerts', function (Blueprint $table): void {
                $table->unsignedTinyInteger('effective_severity')->default(1)->index();
            });
        }

        if (! Schema::hasColumn('disasters', 'default_severity')) {
            Schema::table('disasters', function (Blueprint $table): void {
                $table->unsignedTinyInteger('default_severity')->default(1);
            });
        }
    }
};
