<?php

return [
    'enabled' => env('VOYAGER_SURVEY_ENABLED', true),
    'survey_title' => env('VOYAGER_SURVEY_SURVEY_TITLE', 'Registration'),
    'allowed_slugs' => env('VOYAGER_SURVEY_ALLOWED_SLUGS', ['trainee-enrollment']),
    'not_allowed_slugs' => env('VOYAGER_SURVEY_NOT_ALLOWED_SLUGS', []),
];
