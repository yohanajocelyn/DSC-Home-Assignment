<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = DB::table('roles')->where('name', 'Admin')->first();
        $staffRole = DB::table('roles')->where('name', 'Staff')->first();

        User::updateOrCreate([
            'email' => 'admin@email.com'
        ], [
            'name' => 'Admin',
            'password' => Hash::make('password'),
            'role_id' => $adminRole ? $adminRole->id : null,
        ]);

        User::updateOrCreate([
            'email' => 'staff@email.com'
        ], [
            'name' => 'Staff',
            'password' => Hash::make('password'),
            'role_id' => $staffRole ? $staffRole->id : null,
        ]);
    }
}
