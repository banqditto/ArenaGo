<?php

namespace Database\Seeders;

use App\Models\Field;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Field::updateOrCreate(
            ['name' => 'Vibrant Arena Futsal'],
            [
                'type' => 'Futsal',
                'description' => 'Lapangan futsal vinyl standar internasional dengan pencahayaan LED premium dan fasilitas ruang ganti eksklusif.',
                'price_per_hour' => 150000.00,
                'image_path' => 'fields/futsal.png',
            ]
        );

        Field::updateOrCreate(
            ['name' => 'Skyline Badminton Court'],
            [
                'type' => 'Badminton',
                'description' => 'Lapangan badminton dengan karpet Yonex premium, sirkulasi udara baik, dan bebas silau lampu.',
                'price_per_hour' => 60000.00,
                'image_path' => 'fields/badminton.png',
            ]
        );

        Field::updateOrCreate(
            ['name' => 'Legends Basketball Court'],
            [
                'type' => 'Basketball',
                'description' => 'Lapangan basket indoor dengan lantai kayu jati poles, ring hidrolik standar FIBA, dan tribun penonton.',
                'price_per_hour' => 120000.00,
                'image_path' => 'fields/basketball.png',
            ]
        );

        Field::updateOrCreate(
            ['name' => 'Champions Mini Soccer Hub'],
            [
                'type' => 'Mini Soccer',
                'description' => 'Lapangan mini soccer rumput sintetis berkualitas tinggi (semi-outdoor) dengan drainase air terbaik.',
                'price_per_hour' => 250000.00,
                'image_path' => null,
            ]
        );

        Field::updateOrCreate(
            ['name' => 'Apex Padel Club'],
            [
                'type' => 'Padel',
                'description' => 'Lapangan padel tenis kaca tempered modern berstandar WPT (World Padel Tour), lengkap dengan sewa raket gratis.',
                'price_per_hour' => 180000.00,
                'image_path' => null,
            ]
        );

        Field::updateOrCreate(
            ['name' => 'Spike Zone Volleyball Court'],
            [
                'type' => 'Voli',
                'description' => 'Lapangan voli pasir semi-outdoor dengan net berstandar internasional dan pencahayaan malam hari yang terang.',
                'price_per_hour' => 80000.00,
                'image_path' => null,
            ]
        );
    }
}
