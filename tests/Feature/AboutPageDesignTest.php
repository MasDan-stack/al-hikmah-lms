<?php

test('about page renders with realistic copywriting and antislop design elements', function () {
    $response = $this->get(route('tentang-kami'));

    $response->assertStatus(200);

    // Assert realistic copywriting headings and content
    $response->assertSee("Mendampingi Buah Hati Belajar Al-Qur'an dengan Adab dan Tartil", false);
    $response->assertSee('Kebutuhan Bimbingan Mengaji yang Dekat, Sabar, dan Terarah');
    $response->assertSee('Tiga Pendekatan Utama dalam Setiap Sesi');
    $response->assertSee('Kaidah Tajwid &amp; Makhraj', false);
    $response->assertSee('Talaqqi Privat 1-on-1');
    $response->assertSee('Standar Pengajar yang Terkurasi');
    $response->assertSee('Transparansi Belajar yang Memudahkan Orang Tua');
    $response->assertSee("Jurnal Mutaba'ah Harian Digital", false);

    // Assert design system classes are present
    $response->assertSee('page-hero');
    $response->assertSee('about-image-wrapper');
    $response->assertSee('why-card');
    $response->assertSee('nilai-card');
    $response->assertSee('harapan-list');

    // Assert absence of empty AI buzzwords and fake counters
    $response->assertDontSee('100+');
    $response->assertDontSee('15+');
    $response->assertDontSee('transformative');
    $response->assertDontSee('pivotal moment');

    // Assert absence of em dashes (R-02)
    $content = $response->getContent();
    expect(str_contains($content, '—'))->toBeFalse();
});
