<?php

return [
    'date_format' => 'd/m/Y H:i:s',
    'datetime_format' => 'd/m/Y H:i',
    'date_only_format' => 'd/m/Y',
    'filetypes'       => 'csv,doc,docx,jpeg,jpg,pdf,png,ppt,pptx,rar,txt,xls,xlsx,zip', // used in document uploads
    'pagination'  => [
        'users'     => 100,
        'donations' => 50,
        'students'  => 300
    ],
    'start_year' => 2000,
    'documents' => [
        'donations' => [
            'template' => 'donations-import-template.csv',
            'example'  => 'donations-import-example.csv',
        ],
        'students' => [
            'template' => 'students-import-template.csv',
            'example'  => 'students-import-example.csv',
        ],
        'guide' => 'Assist A Student Program Database Manual.pdf'
    ],
    'validEmailDomains' => [
        'osky.com.au',
        'oskyinteractive.com.au',
        'vinnies.org.au',
        'svdpnt.org.au',
        'svdpqld.org.au',
        'svdpsa.org.au',
        'svdp-vic.org.au',
        'vinniestas.org.au',
        'svdpwa.org.au',
        'svdp.org.au',
        'cosgravesoutter.com.au',
    ],
    'access' => [
        'Full Admin' => [
            'create.users',
            'read.users',
            'update.users',
            'delete.users',
            'create.donations',
            'read.donations',
            'update.donations',
            'delete.donations',
            'update.settings',
            'create.students',
            'read.students',
            'update.students',
            'delete.students',
            'create.certificates',
            'create.reports',
            'read.reports',
            'create.galleries',
            'read.galleries',
            'update.galleries',
            'delete.galleries',
            'update.settings',
            'create.documents',
            'read.documents',
            'update.documents',
            'delete.documents'
        ],
        'Certificates Printer' => [
            'create.donations',
            'read.donations',
            'update.donations',
            'delete.donations',
            'create.students',
            'read.students',
            'update.students',
            'delete.students',
            'create.certificates',
            'read.galleries',
        ],
        'Donations Uploader' => [
            'create.donations',
            'read.donations',
            'update.donations',
            'delete.donations',
            'create.reports',
            'read.reports',
            'read.galleries',
        ],
        'Reports Viewer' => [
            'read.reports',
            'read.galleries',
        ],
    ],
];
