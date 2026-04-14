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
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e0/2022_Toyota_Avanza_1.5_G_Toyota_Safety_Sense_W101RE_%2820220403%29.jpg/1280px-2022_Toyota_Avanza_1.5_G_Toyota_Safety_Sense_W101RE_%2820220403%29.jpg',
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
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2f/Honda_Brio_DD1_FL_1.2_V_Rallye_Red.jpg/1280px-Honda_Brio_DD1_FL_1.2_V_Rallye_Red.jpg',
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
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/86/2018_Mitsubishi_Xpander_Ultimate_1.5_NC1W_%2820190623%29.jpg/1280px-2018_Mitsubishi_Xpander_Ultimate_1.5_NC1W_%2820190623%29.jpg',
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
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/1/15/2022_Toyota_Kijang_Innova_Zenix_V_%28Indonesia%29_front_view.jpg',
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
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f9/Suzuki_Ertiga_NC_FL_1.5_GLX_Hybrid_Snow_White_Pearl.jpg/1280px-Suzuki_Ertiga_NC_FL_1.5_GLX_Hybrid_Snow_White_Pearl.jpg',
            ],
        ];

        foreach ($mobils as $mobil) {
            $vehicle = Vehicle::firstOrCreate(
                ['plat_number' => $mobil['plat_number']],
                $mobil
            );

            if (empty($vehicle->image) && !empty($mobil['image'])) {
                $vehicle->update(['image' => $mobil['image']]);
            }
        }
    }
}
