<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('profiles', 'serial_no')) {
                $table->string('serial_no')->nullable()->after('identification');
            }
            if (! Schema::hasColumn('profiles', 'name_company')) {
                $table->string('name_company')->nullable()->after('notes');
            }
            if (! Schema::hasColumn('profiles', 'telephone')) {
                $table->string('telephone')->nullable()->after('name_company');
            }
            if (! Schema::hasColumn('profiles', 'show_header')) {
                $table->boolean('show_header')->default(true)->after('code_type');
            }
            if (! Schema::hasColumn('profiles', 'buttonbackcolor')) {
                $table->string('buttonbackcolor')->nullable()->after('show_header');
            }
            if (! Schema::hasColumn('profiles', 'buttonfontcolor')) {
                $table->string('buttonfontcolor')->nullable()->after('buttonbackcolor');
            }
            if (! Schema::hasColumn('profiles', 'enable_data_collection')) {
                $table->boolean('enable_data_collection')->default(false)->after('buttonfontcolor');
            }
            if (! Schema::hasColumn('profiles', 'set_up_compulsory')) {
                $table->boolean('set_up_compulsory')->default(false)->after('enable_data_collection');
            }
            if (! Schema::hasColumn('profiles', 'data_collection_mobile')) {
                $table->string('data_collection_mobile')->nullable()->after('set_up_compulsory');
            }
            if (! Schema::hasColumn('profiles', 'data_collection_email')) {
                $table->string('data_collection_email')->nullable()->after('data_collection_mobile');
            }
            if (! Schema::hasColumn('profiles', 'data_collection_name')) {
                $table->string('data_collection_name')->nullable()->after('data_collection_email');
            }
            if (! Schema::hasColumn('profiles', 'data_collection_content')) {
                $table->text('data_collection_content')->nullable()->after('data_collection_name');
            }
            if (! Schema::hasColumn('profiles', 'display_share_link')) {
                $table->boolean('display_share_link')->default(false)->after('data_collection_content');
            }
            if (! Schema::hasColumn('profiles', 'application')) {
                $table->string('application')->nullable()->after('display_share_link');
            }
            if (! Schema::hasColumn('profiles', 'activate_bridge_graphic')) {
                $table->boolean('activate_bridge_graphic')->default(false)->after('application');
            }
            if (! Schema::hasColumn('profiles', 'pop_up_formbuilder')) {
                $table->boolean('pop_up_formbuilder')->default(false)->after('form_is_enable');
            }
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $columns = [
                'serial_no', 'name_company', 'telephone', 'show_header',
                'buttonbackcolor', 'buttonfontcolor', 'enable_data_collection',
                'set_up_compulsory', 'data_collection_mobile', 'data_collection_email',
                'data_collection_name', 'data_collection_content', 'display_share_link',
                'application', 'activate_bridge_graphic', 'pop_up_formbuilder',
            ];

            $existing = array_values(array_filter(
                $columns,
                fn (string $column): bool => Schema::hasColumn('profiles', $column),
            ));

            if ($existing !== []) {
                $table->dropColumn($existing);
            }
        });
    }
};
