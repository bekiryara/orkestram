<?php

return [
    'zones' => [
        'public' => [
            ['label' => 'Ana Sayfa', 'href' => '/'],
            ['label' => 'Ilanlar', 'href' => '/ilanlar'],
            ['label' => 'Giris', 'href' => '/giris', 'guest_only' => true],
            ['label' => 'Kayit Ol', 'href' => '/kayit', 'guest_only' => true],
            [
                'label' => 'Hesabim',
                'href' => '/hesabim',
                'auth_only' => true,            ],
            ['label' => 'Admin', 'href' => '/admin/pages', 'auth_only' => true, 'roles' => ['super_admin', 'admin', 'content_editor', 'listing_editor', 'support_agent', 'viewer']],
        ],
        'portal' => [
            ['label' => 'Giris', 'href' => '/giris', 'guest_only' => true],
            ['label' => 'Hesabim', 'href' => '/hesabim', 'auth_only' => true],
        ],
    ],
];

