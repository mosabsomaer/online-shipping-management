<?php

namespace Database\Seeders;

use App\Models\Port;
use App\Models\Route;
use Illuminate\Database\Seeder;

class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $routes = [
            ['origin_code' => 'TRIST', 'destination_code' => 'LYTIP'],
            ['origin_code' => 'EGALY', 'destination_code' => 'LYBEN'],
            ['origin_code' => 'TNTUN', 'destination_code' => 'LYTIP'],
            ['origin_code' => 'MTMLA', 'destination_code' => 'LYMIS'],
            ['origin_code' => 'AEDXB', 'destination_code' => 'LYTIP'],
        ];

        foreach ($routes as $route) {
            $originPort = Port::where('code', $route['origin_code'])->first();
            $destinationPort = Port::where('code', $route['destination_code'])->first();

            if ($originPort && $destinationPort) {
                Route::firstOrCreate(
                    [
                        'origin_port_id' => $originPort->id,
                        'destination_port_id' => $destinationPort->id,
                    ],
                    [
                        'origin_port_id' => $originPort->id,
                        'destination_port_id' => $destinationPort->id,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
