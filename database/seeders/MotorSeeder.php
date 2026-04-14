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
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7f/Honda_CB150R_Streetfire_SE_-_Jakarta_Fair_2016_-_June_21_2016.jpg/1280px-Honda_CB150R_Streetfire_SE_-_Jakarta_Fair_2016_-_June_21_2016.jpg',
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
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Yamaha_nmax_cpd150_YCP.JPG/1280px-Yamaha_nmax_cpd150_YCP.JPG',
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
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f0/Honda_PCX125_2011_Front.JPG/1280px-Honda_PCX125_2011_Front.JPG',
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
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/00/2009_Kawasaki_Ninja_250R_Black.jpg/1280px-2009_Kawasaki_Ninja_250R_Black.jpg',
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
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/e/e3/2022_Honda_Vario_160_ABS_%2820221105%29.jpg',
            ],
        ];

        foreach ($motors as $motor) {
            $vehicle = Vehicle::firstOrCreate(
                ['plat_number' => $motor['plat_number']],
                $motor
            );

            if (empty($vehicle->image) && !empty($motor['image'])) {
                $vehicle->update(['image' => $motor['image']]);
            }
        }
    }
}
