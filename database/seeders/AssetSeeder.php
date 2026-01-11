<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Materi;
use App\Models\AssetItem;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        // Materi
        $materi1 = Materi::create(['nama' => 'Robotika Basic']);
        $materi2 = Materi::create(['nama' => 'Coding Python']);
        $materi3 = Materi::create(['nama' => 'Science Lab']);

        // Assets
        $asset1 = Asset::create([
            'nama_aset' => 'Lego Mindstorms',
            'status' => 'Tersedia',
            'jumlah' => 5, // Cache count, but we will create 5 items
            'tahun' => 2024,
            'harga' => 5000000
        ]);
        $asset1->materi()->attach([$materi1->id, $materi2->id]);

        // Create Items for Asset 1
        for ($i = 1; $i <= 5; $i++) {
            AssetItem::create([
                'asset_id' => $asset1->id,
                'code' => 'LM-' . $i,
                'condition' => 'Baik',
                'is_available' => true
            ]);
        }

        $asset2 = Asset::create([
            'nama_aset' => 'Microscope',
            'status' => 'Tersedia',
            'jumlah' => 3,
            'tahun' => 2023,
            'harga' => 2000000
        ]);
        $asset2->materi()->attach($materi3->id);

        // Create Items for Asset 2
        for ($i = 1; $i <= 3; $i++) {
            AssetItem::create([
                'asset_id' => $asset2->id,
                'code' => 'MIC-' . $i,
                'condition' => 'Baik',
                'is_available' => true
            ]);
        }

        $asset3 = Asset::create([
            'nama_aset' => 'Laptop ROG',
            'status' => 'Tersedia',
            'jumlah' => 2,
            'tahun' => 2025,
            'harga' => 25000000
        ]);
        $asset3->materi()->attach($materi2->id);

        for ($i = 1; $i <= 2; $i++) {
            AssetItem::create([
                'asset_id' => $asset3->id,
                'code' => 'LPT-' . $i,
                'condition' => 'Baik',
                'is_available' => true
            ]);
        }
    }
}
