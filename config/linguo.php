<?php

return [

    'sources' => [
        base_path('resources/views'),
        base_path('app/Http/Controllers'),
    ] ,

    'i18nPath'     => base_path('resources/lang'),
    'potPathName'  => base_path('resources/lang') . '/catalog.pot',

    'locales'      => ['es_ES', 'en_GB', 'de_DE', 'it_IT', 'fr_FR', 'pt_BR'],
];