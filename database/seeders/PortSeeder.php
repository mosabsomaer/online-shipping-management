<?php

namespace Database\Seeders;

use App\Models\Port;
use Illuminate\Database\Seeder;

class PortSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ports = [
            [
                'name' => 'Tripoli Port',
                'name_ar' => 'ميناء طرابلس',
                'code' => 'LYTIP',
                'latitude' => 32.8925,
                'longitude' => 13.1864,
                'country' => 'LY',
                'is_active' => true,
            ],
            [
                'name' => 'Benghazi Port',
                'name_ar' => 'ميناء بنغازي',
                'code' => 'LYBEN',
                'latitude' => 32.1163,
                'longitude' => 20.0686,
                'country' => 'LY',
                'is_active' => true,
            ],
            [
                'name' => 'Misurata Port',
                'name_ar' => 'ميناء مصراتة',
                'code' => 'LYMIS',
                'latitude' => 32.3754,
                'longitude' => 15.0919,
                'country' => 'LY',
                'is_active' => true,
            ],
            [
                'name' => 'Istanbul Port',
                'name_ar' => 'ميناء إسطنبول',
                'code' => 'TRIST',
                'latitude' => 41.0082,
                'longitude' => 28.9784,
                'country' => 'TR',
                'is_active' => true,
            ],
            [
                'name' => 'Alexandria Port',
                'name_ar' => 'ميناء الإسكندرية',
                'code' => 'EGALY',
                'latitude' => 31.2001,
                'longitude' => 29.9187,
                'country' => 'EG',
                'is_active' => true,
            ],
            [
                'name' => 'Tunis Port',
                'name_ar' => 'ميناء تونس',
                'code' => 'TNTUN',
                'latitude' => 36.8065,
                'longitude' => 10.1815,
                'country' => 'TN',
                'is_active' => true,
            ],
            [
                'name' => 'Valletta Port',
                'name_ar' => 'ميناء فاليتا',
                'code' => 'MTMLA',
                'latitude' => 35.8989,
                'longitude' => 14.5146,
                'country' => 'MT',
                'is_active' => true,
            ],
            [
                'name' => 'Dubai Port',
                'name_ar' => 'ميناء دبي',
                'code' => 'AEDXB',
                'latitude' => 25.2048,
                'longitude' => 55.2708,
                'country' => 'AE',
                'is_active' => true,
            ],
        ];

        foreach ($ports as $port) {
            Port::updateOrCreate(
                ['code' => $port['code']],
                $port
            );
        }
    }
}
