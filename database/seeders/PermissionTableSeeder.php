<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permission = Permission::create(['name' => 'edit']);
        $permission = Permission::create(['name' => 'delete']);
        $permission = Permission::create(['name' => 'publish']);
       
        $role1 = Role::create(['name' => 'doctor']);
        $role1->givePermissionTo('edit');
        

        $role2 = Role::create(['name' => 'user']);
        $role2->givePermissionTo('edit');

        
    }
}
