<?php

return [

    'domain'  => 'default',
    'headers' => [
        'Project-Id-Version'    => 'linguo',
        'Language-Team'         => 'Backtheweb <info@backtheweb.com>'
    ],

    'sources' => [
        base_path('resources/views'),
        base_path('app/Http/Controllers'),
    ] ,

    'i18nPath'     => base_path('resources/lang'),
    'locales'      => ['es', 'en', 'ca_ES'],
];