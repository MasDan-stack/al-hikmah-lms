<?php

test('alerts:scan console command executes successfully', function () {
    $this->artisan('alerts:scan')
        ->expectsOutputToContain('Memulai pemindaian anomali operasional')
        ->assertSuccessful();
});

test('gamification:refresh-leaderboard console command executes successfully', function () {
    $this->artisan('gamification:refresh-leaderboard')
        ->expectsOutputToContain('Cache leaderboard berhasil diperbarui')
        ->assertSuccessful();
});
