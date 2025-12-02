<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if superadmin already exists
        $existingSuperAdmin = DB::table('admin')
            ->where('username', 'superadmin')
            ->first();

        if ($existingSuperAdmin) {
            $this->command->warn('Superadmin already exists. Skipping...');
            return;
        }

        // Insert superadmin
        DB::table('admin')->insert([
            'nama' => 'Super Admin',
            'nama_pasar' => 'All',
            'id_pasar' => 0,
            'username' => 'superadmin',
            'password' => 'superadmin123', // Plain text password
            'role' => 'superadmin',
        ]);

        $this->command->info('Superadmin created successfully!');
        $this->command->info('Username: superadmin');
        $this->command->info('Password: superadmin123');
        $this->command->warn('⚠️  Please change the password after first login!');
    }
}
