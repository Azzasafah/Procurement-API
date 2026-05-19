<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Stock;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Departments
        $itDept  = Department::create(['name' => 'Information Technology', 'code' => 'IT',  'description' => 'Departemen Teknologi Informasi']);
        $finDept = Department::create(['name' => 'Finance',               'code' => 'FIN', 'description' => 'Departemen Keuangan']);
        $hrDept  = Department::create(['name' => 'Human Resources',       'code' => 'HR',  'description' => 'Departemen SDM']);
        $opsDept = Department::create(['name' => 'Operations',            'code' => 'OPS', 'description' => 'Departemen Operasional']);
        $mktDept = Department::create(['name' => 'Marketing',             'code' => 'MKT', 'description' => 'Departemen Marketing']);

        // Users (password: password)
        User::create(['name' => 'Admin System',   'email' => 'admin@procurement.app',     'password' => Hash::make('password'), 'department_id' => $itDept->id,  'role' => 'admin']);
        User::create(['name' => 'Budi Santoso',   'email' => 'employee@procurement.app',  'password' => Hash::make('password'), 'department_id' => $finDept->id, 'role' => 'employee']);
        User::create(['name' => 'Siti Rahayu',    'email' => 'purchasing@procurement.app', 'password' => Hash::make('password'), 'department_id' => $itDept->id,  'role' => 'purchasing']);
        User::create(['name' => 'Andi Wijaya',    'email' => 'manager@procurement.app',   'password' => Hash::make('password'), 'department_id' => $itDept->id,  'role' => 'manager']);
        User::create(['name' => 'Dewi Lestari',   'email' => 'warehouse@procurement.app', 'password' => Hash::make('password'), 'department_id' => $opsDept->id, 'role' => 'warehouse']);

        // Vendors
        Vendor::create(['name' => 'PT Sumber Jaya',   'code' => 'VND-001', 'contact_person' => 'Hendra', 'email' => 'hendra@sumberjaya.co.id',  'phone' => '021-5551234', 'category' => 'Office Supplies', 'is_active' => true]);
        Vendor::create(['name' => 'CV Maju Bersama',  'code' => 'VND-002', 'contact_person' => 'Rini',   'email' => 'rini@majubersama.co.id',   'phone' => '021-5555678', 'category' => 'Electronics',     'is_active' => true]);
        Vendor::create(['name' => 'PT Abadi Makmur',  'code' => 'VND-003', 'contact_person' => 'Doni',   'email' => 'doni@abadimakmur.co.id',   'phone' => '021-5559012', 'category' => 'Furniture',        'is_active' => true]);

        // Stocks
        Stock::create(['item_name' => 'Kertas A4',     'category' => 'Office Supplies', 'quantity' => 100, 'unit' => 'rim',  'location' => 'Gudang A-1', 'minimum_stock' => 10]);
        Stock::create(['item_name' => 'Ballpoint Pen', 'category' => 'Office Supplies', 'quantity' => 200, 'unit' => 'pcs',  'location' => 'Gudang A-2', 'minimum_stock' => 20]);
        Stock::create(['item_name' => 'Monitor 24"',   'category' => 'Electronics',     'quantity' => 5,   'unit' => 'unit', 'location' => 'Gudang B-1', 'minimum_stock' => 2]);
        Stock::create(['item_name' => 'Keyboard',      'category' => 'Electronics',     'quantity' => 10,  'unit' => 'unit', 'location' => 'Gudang B-2', 'minimum_stock' => 3]);
    }
}
