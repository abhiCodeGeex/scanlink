<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $stringDefaults = [
                'form_title' => '',
                'position' => '',
                'name2' => '',
                'description2' => '',
                'gps_coordinates' => '',
                'analytic_key' => '',
                'data_collection_surname' => '',
                'data_collection_btn_text' => '',
                'data_collection_btn_color' => '',
                'link_button_text' => '',
                'link_button_url' => '',
                'link_button_color' => '',
                'mobile' => '',
                'email' => '',
                'voc_first_name' => '',
                'voc_last_name' => '',
                'voc_address' => '',
                'voc_town' => '',
                'voc_state' => '',
                'voc_phone' => '',
                'voc_known_allergies' => '',
                'voc_blood_type' => '',
                'voc_next_of_kin' => '',
                'voc_contact_phone' => '',
                'voc_employer' => '',
                'voc_emp_address' => '',
                'voc_emp_town' => '',
                'voc_emp_state' => '',
                'voc_emp_phone' => '',
                'voc_email_text' => '',
                'voc_email_url' => '',
                'voc_email_sign_line1' => '',
                'voc_email_sign_line2' => '',
                'voc_title_bar_text' => '',
                'voc_title_bar_colour' => '',
                'tiles_order' => '',
                'form_email_tag' => '',
            ];

            foreach ($stringDefaults as $column => $default) {
                if (! Schema::hasColumn('profiles', $column)) {
                    $table->string($column)->default($default);
                }
            }

            if (! Schema::hasColumn('profiles', 'link_button')) {
                $table->boolean('link_button')->default(false);
            }

            if (! Schema::hasColumn('profiles', 'reseller_client_id')) {
                $table->unsignedBigInteger('reseller_client_id')->default(0);
            }

            if (! Schema::hasColumn('profiles', 'form_submission_format')) {
                $table->unsignedTinyInteger('form_submission_format')->default(0);
            }

            if (! Schema::hasColumn('profiles', 'enable_form_analytics')) {
                $table->boolean('enable_form_analytics')->default(false);
            }

            if (! Schema::hasColumn('profiles', 'voc_dob')) {
                $table->date('voc_dob')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $columns = [
                'form_title', 'position', 'name2', 'description2', 'gps_coordinates',
                'analytic_key', 'data_collection_surname', 'data_collection_btn_text',
                'data_collection_btn_color', 'link_button', 'link_button_text',
                'link_button_url', 'link_button_color', 'reseller_client_id', 'mobile',
                'email', 'voc_first_name', 'voc_last_name', 'voc_address', 'voc_town',
                'voc_state', 'voc_phone', 'voc_known_allergies', 'voc_blood_type',
                'voc_next_of_kin', 'voc_contact_phone', 'voc_employer', 'voc_emp_address',
                'voc_emp_town', 'voc_emp_state', 'voc_emp_phone', 'voc_email_text',
                'voc_email_url', 'voc_email_sign_line1', 'voc_email_sign_line2',
                'voc_title_bar_text', 'voc_title_bar_colour', 'tiles_order',
                'form_email_tag', 'form_submission_format', 'enable_form_analytics',
                'voc_dob',
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
