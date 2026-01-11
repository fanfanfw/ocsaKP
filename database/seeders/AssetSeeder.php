<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Materi;
use App\Models\AssetItem;
use App\Models\AssetPart;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        // Materi
        $materi1 = Materi::firstOrCreate(['nama' => 'Robotika Basic']);
        $materi2 = Materi::firstOrCreate(['nama' => 'Coding Python']);
        $materi3 = Materi::firstOrCreate(['nama' => 'Science Lab']);

        // Assets
        $asset1 = Asset::firstOrCreate(
            ['nama_aset' => 'Lego Mindstorms'],
            [
                'status' => 'Tersedia',
                'jumlah' => 5, // Cache count, but we will create 5 items
                'tahun' => 2024,
                'harga' => 5000000,
            ]
        );
        $asset1->materi()->syncWithoutDetaching([$materi1->id, $materi2->id]);

        // Create Items for Asset 1
        for ($i = 1; $i <= 5; $i++) {
            AssetItem::firstOrCreate(
                ['code' => 'LM-' . $i],
                [
                    'asset_id' => $asset1->id,
                    'condition' => 'Baik',
                    'is_available' => true,
                ]
            );
        }

        $asset2 = Asset::firstOrCreate(
            ['nama_aset' => 'Microscope'],
            [
                'status' => 'Tersedia',
                'jumlah' => 3,
                'tahun' => 2023,
                'harga' => 2000000,
            ]
        );
        $asset2->materi()->syncWithoutDetaching([$materi3->id]);

        // Create Items for Asset 2
        for ($i = 1; $i <= 3; $i++) {
            AssetItem::firstOrCreate(
                ['code' => 'MIC-' . $i],
                [
                    'asset_id' => $asset2->id,
                    'condition' => 'Baik',
                    'is_available' => true,
                ]
            );
        }

        $asset3 = Asset::firstOrCreate(
            ['nama_aset' => 'Laptop ROG'],
            [
                'status' => 'Tersedia',
                'jumlah' => 2,
                'tahun' => 2025,
                'harga' => 25000000,
            ]
        );
        $asset3->materi()->syncWithoutDetaching([$materi2->id]);

        for ($i = 1; $i <= 2; $i++) {
            $item = AssetItem::firstOrCreate(
                ['code' => 'LPT-' . $i],
                [
                    'asset_id' => $asset3->id,
                    'condition' => 'Baik',
                    'is_available' => true,
                ]
            );

            // Add Battery Part for each Laptop
            AssetPart::firstOrCreate(
                ['asset_item_id' => $item->id],
                [
                    'asset_id' => $asset3->id,
                    'nama_part' => 'Battery Pack',
                    'kondisi' => 'Baik',
                    'jumlah' => 1,
                    'keterangan' => 'Baterai original',
                ]
            );
        }
    }
}
