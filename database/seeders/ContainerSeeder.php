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
                'name_ar' => 'حاوية قياسية 20 قدم',
                'size' => 33.00,
                'price' => 500.00,
                'weight_limit' => 28000.00,
                'description' => 'Standard 20-foot shipping container. Ideal for general cargo and dry goods.',
                'description_ar' => 'حاوية شحن قياسية 20 قدم. مثالية للبضائع العامة والسلع الجافة.',
                'is_available' => true,
            ],
            [
                'name' => '20ft High Cube Container',
                'name_ar' => 'حاوية عالية 20 قدم',
                'size' => 37.00,
                'price' => 2300.00,
                'weight_limit' => 28000.00,
                'description' => '20-foot high cube container with extra height. Perfect for lightweight, bulky cargo.',
                'description_ar' => 'حاوية عالية 20 قدم بارتفاع إضافي. مثالية للبضائع الخفيفة والضخمة.',
                'is_available' => true,
            ],
            [
                'name' => '40ft Standard Container',
                'name_ar' => 'حاوية قياسية 40 قدم',
                'size' => 67.00,
                'price' => 3500.00,
                'weight_limit' => 26500.00,
                'description' => 'Standard 40-foot shipping container. Double the capacity of 20ft containers.',
                'description_ar' => 'حاوية شحن قياسية 40 قدم. ضعف سعة حاويات 20 قدم.',
                'is_available' => true,
            ],
            [
                'name' => '40ft High Cube Container',
                'name_ar' => 'حاوية عالية 40 قدم',
                'size' => 76.00,
                'price' => 4000.00,
                'weight_limit' => 26500.00,
                'description' => '40-foot high cube container with extra height. Perfect for bulky or lightweight cargo.',
                'description_ar' => 'حاوية عالية 40 قدم بارتفاع إضافي. مثالية للبضائع الضخمة أو الخفيفة.',
                'is_available' => true,
            ],
            [
                'name' => '20ft Refrigerated Container',
                'name_ar' => 'حاوية مبردة 20 قدم',
                'size' => 28.00,
                'price' => 4500.00,
                'weight_limit' => 27000.00,
                'description' => 'Temperature-controlled 20ft container for perishable goods.',
                'description_ar' => 'حاوية 20 قدم مبردة للبضائع القابلة للتلف.',
                'is_available' => true,
            ],
            [
                'name' => '40ft Refrigerated Container',
                'name_ar' => 'حاوية مبردة 40 قدم',
                'size' => 59.00,
                'price' => 7500.00,
                'weight_limit' => 25000.00,
                'description' => 'Temperature-controlled 40ft container for large shipments of perishable goods.',
                'description_ar' => 'حاوية 40 قدم مبردة للشحنات الكبيرة من البضائع القابلة للتلف.',
                'is_available' => true,
            ],
        ];

        foreach ($containers as $container) {
            Container::updateOrCreate(
                ['name' => $container['name']],
                $container
            );
        }
    }
}
