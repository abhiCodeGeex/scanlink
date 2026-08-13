<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('profiles', 'color_code')) {
                $table->string('color_code', 20)->nullable()->after('code_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            if (Schema::hasColumn('profiles', 'color_code')) {
                $table->dropColumn('color_code');
            }
        });
    }
};
