<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('serial_no')->nullable()->after('identification');
            $table->string('name_company')->nullable()->after('notes');
            $table->string('telephone')->nullable()->after('name_company');
            $table->boolean('show_header')->default(true)->after('code_type');
            $table->string('buttonbackcolor')->nullable()->after('show_header');
            $table->string('buttonfontcolor')->nullable()->after('buttonbackcolor');
            $table->boolean('enable_data_collection')->default(false)->after('buttonfontcolor');
            $table->boolean('set_up_compulsory')->default(false)->after('enable_data_collection');
            $table->string('data_collection_mobile')->nullable()->after('set_up_compulsory');
            $table->string('data_collection_email')->nullable()->after('data_collection_mobile');
            $table->string('data_collection_name')->nullable()->after('data_collection_email');
            $table->text('data_collection_content')->nullable()->after('data_collection_name');
            $table->boolean('display_share_link')->default(false)->after('data_collection_content');
            $table->string('application')->nullable()->after('display_share_link');
            $table->boolean('activate_bridge_graphic')->default(false)->after('application');
            $table->boolean('pop_up_formbuilder')->default(false)->after('form_is_enable');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn([
                'serial_no', 'name_company', 'telephone', 'show_header',
                'buttonbackcolor', 'buttonfontcolor', 'enable_data_collection',
                'set_up_compulsory', 'data_collection_mobile', 'data_collection_email',
                'data_collection_name', 'data_collection_content', 'display_share_link',
                'application', 'activate_bridge_graphic', 'pop_up_formbuilder',
            ]);
        });
    }
};
