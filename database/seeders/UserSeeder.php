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

        $adminEmail = config('app.admin.email');
        $adminName = config('app.admin.name');
        $adminPassword = config('app.admin.password');

        User::updateOrCreate([
            'email' => $adminEmail,
        ], [
            'name' => $adminName,
            'password' => Hash::make($adminPassword),
            'role_id' => $adminRole ? $adminRole->id : null,
        ]);
    }
}
