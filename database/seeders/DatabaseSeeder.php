<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Package;
use App\Models\Site;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Admin::firstOrCreate(
            ['email' => 'admin@citinetwifi.com'],
            [
                'name' => 'Super Admin',
                'password' => 'ChangeMe123!', // hashed automatically via the Admin model's cast
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        $sites = [
            ['name' => 'Citinet 1', 'slug' => 'citinet1', 'sort_order' => 1],
            ['name' => 'Citinet 2', 'slug' => 'citinet2', 'sort_order' => 2],
            ['name' => 'Citinet 3', 'slug' => 'citinet3', 'sort_order' => 3],
            ['name' => 'Citinet 4', 'slug' => 'citinet4', 'sort_order' => 4],
        ];

        foreach ($sites as $site) {
            Site::firstOrCreate(['slug' => $site['slug']], $site + ['is_active' => true]);
        }

        $packages = [
            ['name' => '6 Hours', 'price' => 200, 'duration_label' => '6 Hours', 'duration_minutes' => 360, 'device_limit' => 1, 'sort_order' => 1],
            ['name' => 'Daily', 'price' => 300, 'duration_label' => '24 Hours', 'duration_minutes' => 1440, 'device_limit' => 1, 'sort_order' => 2],
            ['name' => 'Weekly', 'price' => 1500, 'duration_label' => '7 Days', 'duration_minutes' => 10080, 'device_limit' => 1, 'sort_order' => 3],
            ['name' => 'Weekly 2 Devices', 'price' => 2500, 'duration_label' => '7 Days', 'duration_minutes' => 10080, 'device_limit' => 2, 'sort_order' => 4],
            ['name' => 'Gaming', 'price' => 2000, 'duration_label' => '7 Days', 'duration_minutes' => 10080, 'device_limit' => 1, 'sort_order' => 5],
            ['name' => 'Monthly', 'price' => 5000, 'duration_label' => '30 Days', 'duration_minutes' => 43200, 'device_limit' => 1, 'sort_order' => 6],
            ['name' => 'Monthly 2 Devices', 'price' => 8000, 'duration_label' => '30 Days', 'duration_minutes' => 43200, 'device_limit' => 2, 'sort_order' => 7],
        ];

        foreach ($packages as $pkg) {
            Package::firstOrCreate(
                ['slug' => Str::slug($pkg['name'])],
                $pkg + ['slug' => Str::slug($pkg['name']), 'is_active' => true]
            );
        }
    }
}
