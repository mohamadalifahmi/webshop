<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@soukelkom.test'],
            ['name' => 'Mohamed (Platform Owner)', 'password' => 'password'],
        );
        $admin->syncRoles([Role::findByName('admin', 'web')]);

        $buyer = User::updateOrCreate(
            ['email' => 'buyer@soukelkom.test'],
            ['name' => 'Demo Buyer', 'password' => 'password'],
        );
        $buyer->assignRole(Role::findByName('buyer', 'web'));

        DB::table('users')->whereNull('email_verified_at')->update(['email_verified_at' => now()]);
    }
}
