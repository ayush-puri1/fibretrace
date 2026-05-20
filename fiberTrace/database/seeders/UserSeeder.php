<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1 Super Admin
        DB::table('users')->insert([
            [
                'name' => 'Super Admin',
                'company_name' => 'FibreTrace',
                'email' => 'superadmin@fibretrace.in',
                'phone' => '0000000000',
                'gstin' => '00AAAAA0000A0Z0',
                'city' => 'System',
                'state' => 'System',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'status' => 'verified',
                'verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Super Admin Secondary',
                'company_name' => 'FibreTrace',
                'email' => 'superadmin@gmail.com',
                'phone' => '0000000009',
                'gstin' => '00AAAAA0000A0Z9',
                'city' => 'System',
                'state' => 'System',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'status' => 'verified',
                'verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);

        $superAdmin = DB::table('users')->where('email', 'superadmin@fibretrace.in')->first();

        // 2 Admins
        DB::table('users')->insert([
            [
                'name' => 'Ayush Puri',
                'company_name' => 'FibreTrace',
                'email' => 'sawanpuri9907@gmail.com',
                'phone' => '0000000001',
                'gstin' => '00AAAAA0000A0Z1',
                'city' => 'System',
                'state' => 'System',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'verified',
                'verified_at' => $now,
                'verified_by' => $superAdmin->id,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Sawan',
                'company_name' => 'FibreTrace',
                'email' => 'sawanpuri011@gmail.com',
                'phone' => '0000000002',
                'gstin' => '00AAAAA0000A0Z2',
                'city' => 'System',
                'state' => 'System',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'verified',
                'verified_at' => $now,
                'verified_by' => $superAdmin->id,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // 2 Sellers
        DB::table('users')->insert([
            [
                'name' => 'Global Textiles Admin',
                'company_name' => 'Global Textiles',
                'email' => 'admin@globaltextiles.com',
                'phone' => '9876543210',
                'password' => Hash::make('password123'),
                'gstin' => '03AAAAA0000A1Z5',
                'role' => 'seller',
                'status' => 'verified',
                'city' => 'Ludhiana',
                'state' => 'Punjab',
                'address' => '123 Textile Park, Ludhiana',
                'verified_at' => $now,
                'verified_by' => $superAdmin->id,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Sunrise Garments Admin',
                'company_name' => 'Sunrise Garments',
                'email' => 'sunrisegarments@gmail.com',
                'phone' => '8765432109',
                'password' => Hash::make('password123'),
                'gstin' => '03CCCCC2222C3X7',
                'role' => 'seller',
                'status' => 'verified',
                'city' => 'Panipat',
                'state' => 'Haryana',
                'address' => '456 Weavers Road, Panipat',
                'verified_at' => $now,
                'verified_by' => $superAdmin->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);

        // 2 Buyers
        DB::table('users')->insert([
            [
                'name' => 'Panipat Spinners Contact',
                'company_name' => 'Panipat Spinners Ltd',
                'email' => 'contact@panipatspinners.in',
                'phone' => '7654321098',
                'password' => Hash::make('password123'),
                'gstin' => '06BBBBB1111B2Y6',
                'role' => 'buyer',
                'status' => 'verified',
                'city' => 'Panipat',
                'state' => 'Haryana',
                'address' => '789 Spinners Lane, Panipat',
                'verified_at' => $now,
                'verified_by' => $superAdmin->id,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Haryana Threads Info',
                'company_name' => 'Haryana Threads',
                'email' => 'info@haryanathreads.com',
                'phone' => '6543210987',
                'password' => Hash::make('password123'),
                'gstin' => '06DDDDD3333D4W8',
                'role' => 'buyer',
                'status' => 'verified',
                'city' => 'Karnal',
                'state' => 'Haryana',
                'address' => '101 Industrial Estate, Karnal',
                'verified_at' => $now,
                'verified_by' => $superAdmin->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);

        // 2 Pending Users
        DB::table('users')->insert([
            [
                'name' => 'Pending Seller',
                'company_name' => 'Pending Textiles',
                'email' => 'pending@textiles.com',
                'phone' => '5432109876',
                'password' => Hash::make('password123'),
                'gstin' => '03EEEEE5555E5Z1',
                'role' => 'seller',
                'status' => 'pending',
                'city' => 'Amritsar',
                'state' => 'Punjab',
                'address' => 'Pending Area',
                'verified_at' => null,
                'verified_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Pending Buyer',
                'company_name' => 'Pending Traders',
                'email' => 'pending@traders.com',
                'phone' => '4321098765',
                'password' => Hash::make('password123'),
                'gstin' => '06FFFFF6666F6X2',
                'role' => 'buyer',
                'status' => 'pending',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'address' => 'New Delhi',
                'verified_at' => null,
                'verified_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);

        // 1 Rejected User
        DB::table('users')->insert([
            'name' => 'Rejected Buyer',
            'company_name' => 'Bad Traders',
            'email' => 'rejected@traders.com',
            'phone' => '3210987654',
            'password' => Hash::make('password123'),
            'gstin' => '06GGGGG7777G7Y3',
            'role' => 'buyer',
            'status' => 'rejected',
            'rejection_reason' => 'Invalid GSTIN format',
            'city' => 'Delhi',
            'state' => 'Delhi',
            'address' => 'New Delhi',
            'verified_at' => null,
            'verified_by' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
