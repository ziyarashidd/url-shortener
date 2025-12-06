<?php

// database/seeders/UserSeeder.php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();

        // Create SuperAdmin (not assigned to any company initially)
        User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'company_id' => $company->id, // SuperAdmin still needs company_id
                'role_id' => Role::where('name', 'SuperAdmin')->first()->id,
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );

        // Create Admin
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'company_id' => $company->id,
                'role_id' => Role::where('name', 'Admin')->first()->id,
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        // Create Member
        User::firstOrCreate(
            ['email' => 'member@example.com'],
            [
                'company_id' => $company->id,
                'role_id' => Role::where('name', 'Member')->first()->id,
                'name' => 'Member User',
                'password' => Hash::make('password'),
            ]
        );

        // Create Sales
        User::firstOrCreate(
            ['email' => 'sales@example.com'],
            [
                'company_id' => $company->id,
                'role_id' => Role::where('name', 'Sales')->first()->id,
                'name' => 'Sales User',
                'password' => Hash::make('password'),
            ]
        );

        // Create Manager
        User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'company_id' => $company->id,
                'role_id' => Role::where('name', 'Manager')->first()->id,
                'name' => 'Manager User',
                'password' => Hash::make('password'),
            ]
        );
    }
}