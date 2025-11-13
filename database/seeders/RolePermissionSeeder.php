<?php
// database/seeders/RolePermissionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions grouped by domain
        $groups = [
            'user' => ['view','create','edit','delete'],
            'role' => ['view','create','edit','delete'],
            'permission' => ['view','assign'],
            'satuan' => ['view','create','edit','delete'],
            'proposal' => ['view','create','edit','delete'],
            'annualbudget' => ['view','create','edit','delete'],
            'approval' => ['view','create','edit','delete'],
            'realization' => ['view','create','edit','delete'],
            'attachment' => ['view','create','delete'],
            'pengumuman' => ['view','create','edit','delete'],
        ];

        $allPermissions = [];
        foreach ($groups as $prefix => $actions) {
            foreach ($actions as $action) {
                $allPermissions[] = "$prefix.$action";
            }
        }

        // Create or update permissions idempotently
        foreach ($allPermissions as $name) {
            Permission::findOrCreate($name);
        }

        // Create roles idempotently
        $superAdminRole = Role::findOrCreate('Super Admin');
        // $adminRole = Role::findOrCreate('Admin');
        $perencanaRole = Role::findOrCreate('Perencana');
        $verifikatorRole = Role::findOrCreate('Verifikator');
        // $pimpinanRole = Role::findOrCreate('Pimpinan');
        // $bendaharaRole = Role::findOrCreate('Bendahara');

        // Assign permissions
        $superAdminRole->syncPermissions(Permission::all());

        // $adminRole->syncPermissions([
        //     // Full management for core modules
        //     'user.view','user.create','user.edit','user.delete',
        //     'role.view','role.create','role.edit','role.delete',
        //     'permission.view','permission.assign',

        //     // Domain modules
        //     'satuan.view','satuan.create','satuan.edit','satuan.delete',
        //     'proposal.view','proposal.create','proposal.edit','proposal.delete',
        //     'annualbudget.view','annualbudget.create','annualbudget.edit','annualbudget.delete',
        //     'approval.view','approval.create','approval.edit','approval.delete',
        //     'realization.view','realization.create','realization.edit','realization.delete',
        //     'attachment.view','attachment.create','attachment.delete',
        // ]);

        $perencanaRole->syncPermissions([
            'proposal.view','proposal.create','proposal.edit',
            'annualbudget.view','annualbudget.create','annualbudget.edit',
            'approval.view','approval.create',
            'attachment.view','attachment.create',
        ]);

        $verifikatorRole->syncPermissions([
            'proposal.view',
            'approval.view','approval.create','approval.edit',
        ]);

        // $pimpinanRole->syncPermissions([
        //     'proposal.view',
        //     'approval.view','approval.create',
        // ]);

        // $bendaharaRole->syncPermissions([
        //     'realization.view','realization.create','realization.edit',
        //     'attachment.view','attachment.create',
        // ]);

        // Create default users if missing and assign roles
        $superAdmin = User::firstOrCreate(
            ['email' => env('SEED_SUPERADMIN_EMAIL', 'superadmin@example.com')],
            [
                'name' => 'Super Admin',
                'password' => Hash::make(env('SEED_SUPERADMIN_PASSWORD', 'password')),
                'active' => true,
            ]
        );
        if (!$superAdmin->hasRole($superAdminRole)) {
            $superAdmin->syncRoles([$superAdminRole]);
        }

        // $admin = User::firstOrCreate(
        //     ['email' => env('SEED_ADMIN_EMAIL', 'admin@example.com')],
        //     [
        //         'name' => 'Administrator',
        //         'password' => Hash::make(env('SEED_ADMIN_PASSWORD', 'password')),
        //         'active' => true,
        //     ]
        // );
        // if (!$admin->hasRole($adminRole)) {
        //     $admin->syncRoles([$adminRole]);
        // }

        $planner = User::firstOrCreate(
            ['email' => env('SEED_PLANNER_EMAIL', 'planner@example.com')],
            [
                'name' => 'Perencana',
                'password' => Hash::make(env('SEED_PLANNER_PASSWORD', 'password')),
                'active' => true,
            ]
        );
        if (!$planner->hasRole($perencanaRole)) {
            $planner->syncRoles([$perencanaRole]);
        }
    }
}
