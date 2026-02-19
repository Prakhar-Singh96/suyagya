<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset Cached Roles and Permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Create Permissions with Sections
        // Yahan hum key (Name) aur value (Section) ka structure use karenge
        $permissions = [
            // Dashboard Section
            ['name' => 'view_dashboard', 'section' => 'dashboard'],

            // User Management Section
            ['name' => 'manage_users', 'section' => 'user'],

            // Seller Management Section
            ['name' => 'approve_seller', 'section' => 'seller'],
            ['name' => 'view_pending_seller', 'section' => 'seller'],
            ['name' => 'view_all_sellers', 'section' => 'seller'],

            // Product Management Section (Example from your image)
            ['name' => 'add_new_product', 'section' => 'product'],
            ['name' => 'show_all_products', 'section' => 'product'],
            ['name' => 'product_edit', 'section' => 'product'],
            ['name' => 'product_delete', 'section' => 'product'],
            ['name' => 'product_bulk_import', 'section' => 'product'],

            // Product Category Section
            ['name' => 'view_product_categories', 'section' => 'product_category'],
            ['name' => 'add_product_category', 'section' => 'product_category'],
            ['name' => 'edit_product_category', 'section' => 'product_category'],
            ['name' => 'delete_product_category', 'section' => 'product_category'],

            // Order Management
            ['name' => 'view_orders', 'section' => 'order'],
            ['name' => 'manage_orders', 'section' => 'order'],

            // Settings
            ['name' => 'manage_settings', 'section' => 'settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission['name'],
                'section' => $permission['section'], // Ab ye section column me save hoga
                'guard_name' => 'web'
            ]);
        }

        // 3. Create Roles and Assign Permissions

        // ROLE: Super Admin (Sab kuch kar sakta hai)
        $superAdminRole = Role::create(['name' => 'superadmin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // ROLE: Staff (Custom Permissions)
        $staffRole = Role::create(['name' => 'staff']);
        $staffRole->givePermissionTo([
            'view_dashboard',
            'approve_seller',
            'add_new_product',
            'show_all_products'
        ]);

        // ROLE: Seller
        $sellerRole = Role::create(['name' => 'seller']);
        // Seller ko usually specific permissions di jati hain
        $sellerRole->givePermissionTo([
            'view_dashboard',
            'add_new_product',
            'show_all_products'
        ]);

        // 4. Create Default SUPER ADMIN User
        $adminUser = User::create([
            'name' => 'Suyagya Admin',
            'phone' => '9870271533',
            'email' => 'admin@suyagya.com',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        UserDetail::create([
            'user_id' => $adminUser->id,
        ]);

        $adminUser->assignRole($superAdminRole);

        echo "Permissions with Sections Created & Super Admin Assigned!\n";
    }
}
