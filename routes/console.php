<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('seed:if-empty', function () {
    if (\App\Models\User::count() > 0) {
        $this->info('Database already seeded, skipping.');
        return;
    }

    $this->call('db:seed', ['--force' => true]);
    $this->info('Database seeded.');
})->purpose('Seed the database only when it is empty');
