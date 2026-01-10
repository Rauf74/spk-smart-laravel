<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Main Database Seeder.
 * 
 * Jalankan dengan: php artisan db:seed
 * Atau dengan migrate: php artisan migrate:fresh --seed
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * 
     * Urutan penting karena ada foreign key dependencies:
     * 1. Users (tidak ada dependency)
     * 2. Kriteria (tidak ada dependency)
     * 3. Subkriteria (depends on Kriteria)
     * 4. Alternatif (tidak ada dependency)
     * 5. Pertanyaan (depends on Kriteria & Alternatif)
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            KriteriaSeeder::class,
            SubkriteriaSeeder::class,
            AlternatifSeeder::class,
            PertanyaanSeeder::class,
        ]);
    }
}
