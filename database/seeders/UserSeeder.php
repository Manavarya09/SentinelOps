<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organization = \App\Models\Organization::first();

        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@acme.com',
            'organization_id' => $organization->id,
            'role' => 'admin',
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@acme.com',
            'organization_id' => $organization->id,
            'role' => 'user',
        ]);
    }
}
