<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RegionalSeeder extends Seeder
{
    public function run(): void
    {
        // Taruh file CSV di: storage/app/reg_provinces.csv dst
        // (biar gampang: php artisan storage:link gak wajib; ini cuma baca file)
        $base = storage_path('app');

        $this->importProvinces($base . '/reg_provinces.csv');
        $this->importRegencies($base . '/reg_regencies.csv');
        $this->importDistricts($base . '/reg_districts.csv');
        $this->importVillages($base . '/reg_villages.csv');
    }

    private function readCsvNoHeader(string $path): \Generator
    {
        if (!File::exists($path)) {
            throw new \RuntimeException("CSV tidak ditemukan: {$path}");
        }

        $handle = fopen($path, 'r');
        if (!$handle) {
            throw new \RuntimeException("Gagal membuka CSV: {$path}");
        }

        while (($row = fgetcsv($handle)) !== false) {
            if (!$row || count($row) === 0) continue;
            yield $row;
        }

        fclose($handle);
    }

    private function importProvinces(string $path): void
    {
        DB::table('reg_provinces')->truncate();

        $batch = [];
        foreach ($this->readCsvNoHeader($path) as $row) {
            // CSV kamu: col_1=code, col_2=name
            $batch[] = [
                'code' => (int) $row[0],
                'name' => (string) $row[1],
            ];

            if (count($batch) >= 1000) {
                DB::table('reg_provinces')->insert($batch);
                $batch = [];
            }
        }

        if ($batch) DB::table('reg_provinces')->insert($batch);
    }

    private function importRegencies(string $path): void
    {
        DB::table('reg_regencies')->truncate();

        $batch = [];
        foreach ($this->readCsvNoHeader($path) as $row) {
            // CSV kamu: col_1=code, col_2=province_code, col_3=name
            $batch[] = [
                'code' => (int) $row[0],
                'province_code' => (int) $row[1],
                'name' => (string) $row[2],
            ];

            if (count($batch) >= 2000) {
                DB::table('reg_regencies')->insert($batch);
                $batch = [];
            }
        }

        if ($batch) DB::table('reg_regencies')->insert($batch);
    }

    private function importDistricts(string $path): void
    {
        DB::table('reg_districts')->truncate();

        $batch = [];
        foreach ($this->readCsvNoHeader($path) as $row) {
            // CSV kamu: col_1=code, col_2=regency_code, col_3=name
            $batch[] = [
                'code' => (int) $row[0],
                'regency_code' => (int) $row[1],
                'name' => (string) $row[2],
            ];

            if (count($batch) >= 5000) {
                DB::table('reg_districts')->insert($batch);
                $batch = [];
            }
        }

        if ($batch) DB::table('reg_districts')->insert($batch);
    }

    private function importVillages(string $path): void
    {
        DB::table('reg_villages')->truncate();

        $batch = [];
        foreach ($this->readCsvNoHeader($path) as $row) {
            // CSV kamu: col_1=code, col_2=district_code, col_3=name
            $batch[] = [
                'code' => (int) $row[0],
                'district_code' => (int) $row[1],
                'name' => (string) $row[2],
            ];

            if (count($batch) >= 10000) {
                DB::table('reg_villages')->insert($batch);
                $batch = [];
            }
        }

        if ($batch) DB::table('reg_villages')->insert($batch);
    }
}