<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class MobilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mobils = [
            [
                'name' => 'Toyota Avanza 1.5 G',
                'vehicle_type' => 'mobil',
                'plat_number' => 'B 1111 MOB',
                'transmission' => 'Manual',
                'year' => 2023,
                'daily_price' => 350000,
                'status' => 'available',
                'description' => 'Mobil keluarga yang irit bahan bakar dan tangguh di berbagai medan.',
            ],
            [
                'name' => 'Honda Brio Satya E',
                'vehicle_type' => 'mobil',
                'plat_number' => 'B 2222 MOB',
                'transmission' => 'Otomatis',
                'year' => 2024,
                'daily_price' => 300000,
                'status' => 'available',
                'description' => 'Hatchback lincah dan hemat bensin, sangat cocok untuk jalanan perkotaan yang padat.',
            ],
            [
                'name' => 'Mitsubishi Xpander Ultimate',
                'vehicle_type' => 'mobil',
                'plat_number' => 'B 3333 MOB',
                'transmission' => 'Otomatis',
                'year' => 2023,
                'daily_price' => 450000,
                'status' => 'available',
                'description' => 'MPV dengan desain futuristik dan ruang kabin yang sangat luas dan nyaman.',
            ],
            [
                'name' => 'Toyota Innova Zenix',
                'vehicle_type' => 'mobil',
                'plat_number' => 'B 4444 MOB',
                'transmission' => 'Otomatis',
                'year' => 2024,
                'daily_price' => 700000,
                'status' => 'available',
                'description' => 'Mobil premium keluarga dengan fitur mewah dan bantingan super empuk.',
            ],
            [
                'name' => 'Suzuki Ertiga Hybrid',
                'vehicle_type' => 'mobil',
                'plat_number' => 'B 5555 MOB',
                'transmission' => 'Otomatis',
                'year' => 2023,
                'daily_price' => 400000,
                'status' => 'available',
                'description' => 'MPV ramah lingkungan berteknologi hybrid yang super irit dan nyaman digunakan.',
            ],
        ];

        foreach ($mobils as $mobil) {
            Vehicle::firstOrCreate(
                ['plat_number' => $mobil['plat_number']],
                $mobil
            );
        }
    }
}
