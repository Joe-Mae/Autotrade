<?php

namespace Database\Seeders;

use App\Models\MarqueeItem;
use Illuminate\Database\Seeder;

class MarqueeSeeder extends Seeder
{
    public function run(): void
    {
        if (MarqueeItem::count() !== 0) {
            return;
        }

        $defaults = [
            ['image' => 'assets/car-vortex.jpg',  'nom' => 'Vortex SV',     'spec' => '740 HP / 0-100 2.8s',       'sort_order' => 10],
            ['image' => 'assets/car-electron.jpg', 'nom' => 'Electron Z',   'spec' => '100% Electric Performance', 'sort_order' => 20],
            ['image' => 'assets/car-heritage.jpg', 'nom' => 'Heritage V8',  'spec' => 'Timeless Power',            'sort_order' => 30],
        ];

        foreach ($defaults as $item) {
            MarqueeItem::create($item);
        }
    }
}