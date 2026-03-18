<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class MotorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $motors = [
            [
                'name' => 'Honda CB150R',
                'vehicle_type' => 'motor',
                'plat_number' => 'B 1234 MTR',
                'transmission' => 'Manual',
                'year' => 2023,
                'daily_price' => 75000,
                'status' => 'available',
                'description' => 'Motor sport Honda CB150R StreetFire, nyaman untuk harian dan touring jarak dekat.',
            ],
            [
                'name' => 'Yamaha NMAX 155',
                'vehicle_type' => 'motor',
                'plat_number' => 'B 5678 MTR',
                'transmission' => 'Otomatis',
                'year' => 2024,
                'daily_price' => 100000,
                'status' => 'available',
                'description' => 'Skutik premium Yamaha NMAX 155 dengan fitur lengkap, cocok untuk touring.',
            ],
            [
                'name' => 'Honda PCX 160',
                'vehicle_type' => 'motor',
                'plat_number' => 'B 9012 MTR',
                'transmission' => 'Otomatis',
                'year' => 2024,
                'daily_price' => 120000,
                'status' => 'available',
                'description' => 'Skutik premium Honda PCX 160 dengan desain elegan dan performa handal.',
            ],
            [
                'name' => 'Kawasaki Ninja 250',
                'vehicle_type' => 'motor',
                'plat_number' => 'B 3456 MTR',
                'transmission' => 'Manual',
                'year' => 2023,
                'daily_price' => 200000,
                'status' => 'available',
                'description' => 'Motor sport Kawasaki Ninja 250 4-silinder, performa tinggi untuk pengalaman berkendara maksimal.',
            ],
            [
                'name' => 'Honda Vario 160',
                'vehicle_type' => 'motor',
                'plat_number' => 'B 7890 MTR',
                'transmission' => 'Otomatis',
                'year' => 2024,
                'daily_price' => 85000,
                'status' => 'available',
                'description' => 'Honda Vario 160 dengan teknologi eSP+, irit bahan bakar dan gesit di perkotaan.',
            ],
        ];

        foreach ($motors as $motor) {
            Vehicle::firstOrCreate(
                ['plat_number' => $motor['plat_number']],
                $motor
            );
        }
    }
}
