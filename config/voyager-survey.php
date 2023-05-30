<?php

return [
    'enabled' => env('VOYAGER_SURVEY_ENABLED', true),
    'survey_name' => env('VOYAGER_SURVEY_NAME', 'Form'),
    'survey_title' => env('VOYAGER_SURVEY_SURVEY_TITLE', 'Registration'),
    'allowed_slugs' => env('VOYAGER_SURVEY_ALLOWED_SLUGS', ['trainee-enrollment']),
    'not_allowed_slugs' => env('VOYAGER_SURVEY_NOT_ALLOWED_SLUGS', []),
    'allowed_slugs_row' => env('VOYAGER_SURVEY_ALLOWED_SLUGS_ROW', ['training']),
    'not_allowed_slugs_row' => env('VOYAGER_SURVEY_NOT_ALLOWED_SLUGS_ROW', []),
];
