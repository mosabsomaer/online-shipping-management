<?php

namespace Database\Seeders;

use App\Models\Container;
use Illuminate\Database\Seeder;

class ContainerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $containers = [
            [
                'name' => '20ft Standard Container',
                'size' => 33.00,
                'price' => 500.00,
                'weight_limit' => 28000.00,
                'description' => 'Standard 20-foot shipping container. Ideal for general cargo and dry goods.',
                'is_available' => true,
            ],
            [
                'name' => '20ft High Cube Container',
                'size' => 37.00,
                'price' => 2300.00,
                'weight_limit' => 28000.00,
                'description' => '20-foot high cube container with extra height. Perfect for lightweight, bulky cargo.',
                'is_available' => true,
            ],
            [
                'name' => '40ft Standard Container',
                'size' => 67.00,
                'price' => 3500.00,
                'weight_limit' => 26500.00,
                'description' => 'Standard 40-foot shipping container. Double the capacity of 20ft containers.',
                'is_available' => true,
            ],
            [
                'name' => '40ft High Cube Container',
                'size' => 76.00,
                'price' => 4000.00,
                'weight_limit' => 26500.00,
                'description' => '40-foot high cube container with extra height. Perfect for bulky or lightweight cargo.',
                'is_available' => true,
            ],
            [
                'name' => '20ft Refrigerated Container',
                'size' => 28.00,
                'price' => 4500.00,
                'weight_limit' => 27000.00,
                'description' => 'Temperature-controlled 20ft container for perishable goods.',
                'is_available' => true,
            ],
            [
                'name' => '40ft Refrigerated Container',
                'size' => 59.00,
                'price' => 7500.00,
                'weight_limit' => 25000.00,
                'description' => 'Temperature-controlled 40ft container for large shipments of perishable goods.',
                'is_available' => true,
            ],
        ];

        foreach ($containers as $container) {
            Container::create($container);
        }
    }
}
