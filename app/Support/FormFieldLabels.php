<?php

namespace App\Support;

/**
 * Map database column names to form labels for friendly validation messages.
 */
class FormFieldLabels
{
    /**
     * @var array<string, string>
     */
    private const LABELS = [
        'password' => 'Password',
        'code_profile_name' => 'Code Profile Name',
        'name' => 'Name',
        'identification' => 'Identification',
        'serial_no' => 'Serial No.',
        'address' => 'Address',
        'description' => 'Description',
        'notes' => 'Notes',
        'url' => 'Destination URL',
        'gps_coordinates' => 'GPS Coordinates',
        'application' => 'Application',
        'telephone' => 'Telephone',
        'mobile' => 'Mobile',
        'email' => 'Email',
        'url' => 'Website',
        'name_company' => 'Contact Person / Company',
        'form_title' => 'Form display title',
        'data_collection_content' => 'Content',
        'data_collection_btn_text' => 'Button text',
        'data_collection_btn_color' => 'Button colour',
        'data_collection_name' => 'Name',
        'data_collection_surname' => 'Surname',
        'data_collection_email' => 'Email',
        'data_collection_mobile' => 'Mobile',
        'link_button_text' => 'Button Text',
        'link_button_url' => 'Button Link',
        'link_button_color' => 'Colour',
        'color_code' => 'Colour Selector',
        'checklist_item' => 'Checklist item',
        'client_id' => 'Client',
        'user_id' => 'Profile owner',
        'type_id' => 'Profile Type',
        'expired_at' => 'Expiry date',
        'activation_start_date' => 'Start Date',
        'activation_end_date' => 'End Date',
        'txt_footer' => 'Picture caption',
        'picture_name' => 'Picture file',
        'logo_name' => 'Logo file',
        'doc_name' => 'Document file',
        'btn_color' => 'Button colour',
        'video_name' => 'Video URL or ID',
        'title' => 'Video title',
    ];

    public static function for(string $column): string
    {
        if (isset(self::LABELS[$column])) {
            return self::LABELS[$column];
        }

        return str($column)
            ->replace('_', ' ')
            ->replace('txt ', '')
            ->trim()
            ->title()
            ->toString();
    }

    /**
     * Filament form state path for a top-level profile attribute.
     */
    public static function formStatePath(string $column): string
    {
        return 'data.'.$column;
    }
}
