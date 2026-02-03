<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Revision Model
    |--------------------------------------------------------------------------
    */
    'saxon_xslt_parser' => env('SAXON_XSLT_PARSER', false),
    'saxon_schema_validate' => env('SAXON_SCHEMA_VALIDATE', false),
    'saxon_license_file_path' => env('SAXON_LICENSE_FILE_PATH', ''),

    'opera_url' => env('OPERA_URL'),
    'opera_username' => env('OPERA_USERNAME'),
    'opera_password' => env('OPERA_PASSWORD'),

    'api_secret' => env('API_SECRET'),

    'copy_asset_to_ftp' =>  env('COPY_ASSET_TO_FTP', false),

    'ftp_initial_path' =>  env('FTP_INITIAL_PATH', ''),

    'itp_image_path' => env('ITP_IMAGE_PATH', ''),

    'opera_project_id' => env('OPERA_PROJECT_ID'),

    'use_smart_ce_api' => env('USE_SMART_CE', false),
    'smart_ce_url' => env('SMART_CE_URL', ''),

    'enable_style_toolbar' => env('ENABLE_STYLE_TOOLBAR', false),
    'enable_track_changes' => env('ENABLE_TRACK_CHANGES', false),
    'auto_save_authoring_file' => env('ENABLE_AUTO_SAVE_AUTHORING_FILE', true),

    'use_hyperlink' => env('USE_HYPERLINK', false),
    'ftp_initial_path_hyperlink' =>  env('FTP_INITIAL_PATH_HYPERLINK', ''),
    'xslt_error_notify_to' =>  env('XSLT_ERROR_NOTIFY_TO', ''),
    'deployed_env' =>  env('DEPLOYED_ENV', ''),

    'ebs_typefi_root_folder' =>  env('EBS_TYPEFI_ROOT_FOLDER', 'TypefiStorage'),
    'ebs_app_root_folder' =>  env('EBS_APP_ROOT_FOLDER', 'AppStorage'),
    'ebs_host' =>  env('EBS_HOST', ''),
    'typefi_host' =>  env('TYPEFI_HOST', ''),
    'typefi_base_path' =>  env('TYPEFI_BASE_PATH', ''),
    'typefi_username' =>  env('TYPEFI_USERNAME', ''),
    'typefi_password' =>  env('TYPEFI_PASSWORD', ''),
    'blinkenlights_queue_id' =>  env('BLINKENLIGHTS_QUEUE_ID', 'any'),
    'reference_caption_text_length' =>  30,
];
