<?php

return [
    'by_role' => [
        'customer' => [
            ['label' => 'Taleplerim', 'href' => '/customer/requests', 'description' => 'Gonderdigin talepleri takip et.'],
            ['label' => 'Yeni Talep Olustur', 'href' => '/customer', 'description' => 'Yeni etkinlik talebini olustur.'],
        ],
        'listing_owner' => [
            ['label' => 'Ilanlarim', 'href' => '/owner/listings', 'description' => 'Ilanlarini listele ve yonet.'],
            ['label' => 'Ilan Ekle', 'href' => '/owner/listings/create', 'description' => 'Yeni ilan olustur.'],
            ['label' => 'Leadler', 'href' => '/owner/leads', 'description' => 'Gelen musteri taleplerini takip et.'],
        ],
        'support_agent' => [
            ['label' => 'Support Talepler', 'href' => '/support/requests', 'description' => 'Talep kayitlarini izle ve guncelle.'],
        ],
        'admin' => [
            ['label' => 'Admin Sayfalar', 'href' => '/admin/pages', 'description' => 'Icerik yonetim ekranina git.'],
        ],
        'super_admin' => [
            ['label' => 'Admin Sayfalar', 'href' => '/admin/pages', 'description' => 'Yonetim paneline git.'],
        ],
        'content_editor' => [
            ['label' => 'Admin Sayfalar', 'href' => '/admin/pages', 'description' => 'Icerik yonetim ekranina git.'],
        ],
        'listing_editor' => [
            ['label' => 'Ilan Yonetimi', 'href' => '/admin/listings', 'description' => 'Ilan kayitlarini yonet.'],
        ],
        'viewer' => [
            ['label' => 'Admin Sayfalar', 'href' => '/admin/pages', 'description' => 'Salt-okuma erisimine git.'],
        ],
    ],
];
