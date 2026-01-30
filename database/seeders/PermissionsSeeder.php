<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Permisos básicos del sistema
        $permissions = [
            // Productos
            ['name' => 'products.view', 'display_name' => 'Ver Productos', 'description' => 'Puede ver el listado de productos'],
            ['name' => 'products.create', 'display_name' => 'Crear Productos', 'description' => 'Puede crear nuevos productos'],
            ['name' => 'products.edit', 'display_name' => 'Editar Productos', 'description' => 'Puede editar productos existentes'],
            ['name' => 'products.delete', 'display_name' => 'Eliminar Productos', 'description' => 'Puede eliminar productos'],

            // Categorías
            ['name' => 'categories.view', 'display_name' => 'Ver Categorías', 'description' => 'Puede ver el listado de categorías'],
            ['name' => 'categories.create', 'display_name' => 'Crear Categorías', 'description' => 'Puede crear nuevas categorías'],
            ['name' => 'categories.edit', 'display_name' => 'Editar Categorías', 'description' => 'Puede editar categorías existentes'],
            ['name' => 'categories.delete', 'display_name' => 'Eliminar Categorías', 'description' => 'Puede eliminar categorías'],

            // Ventas
            ['name' => 'sales.view', 'display_name' => 'Ver Ventas', 'description' => 'Puede ver el historial de ventas'],
            ['name' => 'sales.create', 'display_name' => 'Crear Ventas', 'description' => 'Puede realizar ventas'],
            ['name' => 'sales.edit', 'display_name' => 'Editar Ventas', 'description' => 'Puede modificar ventas'],
            ['name' => 'sales.delete', 'display_name' => 'Anular Ventas', 'description' => 'Puede anular ventas'],

            // Clientes
            ['name' => 'customers.view', 'display_name' => 'Ver Clientes', 'description' => 'Puede ver el listado de clientes'],
            ['name' => 'customers.create', 'display_name' => 'Crear Clientes', 'description' => 'Puede registrar nuevos clientes'],
            ['name' => 'customers.edit', 'display_name' => 'Editar Clientes', 'description' => 'Puede editar información de clientes'],
            ['name' => 'customers.delete', 'display_name' => 'Eliminar Clientes', 'description' => 'Puede eliminar clientes'],

            // Pagos
            ['name' => 'payments.view', 'display_name' => 'Ver Pagos', 'description' => 'Puede ver el historial de pagos'],
            ['name' => 'payments.create', 'display_name' => 'Registrar Pagos', 'description' => 'Puede registrar pagos de clientes'],
            ['name' => 'payments.edit', 'display_name' => 'Editar Pagos', 'description' => 'Puede modificar pagos'],
            ['name' => 'payments.delete', 'display_name' => 'Eliminar Pagos', 'description' => 'Puede eliminar pagos'],

            // Compras
            ['name' => 'purchases.view', 'display_name' => 'Ver Compras', 'description' => 'Puede ver el historial de compras'],
            ['name' => 'purchases.create', 'display_name' => 'Crear Compras', 'description' => 'Puede registrar compras'],
            ['name' => 'purchases.edit', 'display_name' => 'Editar Compras', 'description' => 'Puede modificar compras'],
            ['name' => 'purchases.delete', 'display_name' => 'Eliminar Compras', 'description' => 'Puede eliminar compras'],

            // Proveedores
            ['name' => 'suppliers.view', 'display_name' => 'Ver Proveedores', 'description' => 'Puede ver el listado de proveedores'],
            ['name' => 'suppliers.create', 'display_name' => 'Crear Proveedores', 'description' => 'Puede registrar nuevos proveedores'],
            ['name' => 'suppliers.edit', 'display_name' => 'Editar Proveedores', 'description' => 'Puede editar información de proveedores'],
            ['name' => 'suppliers.delete', 'display_name' => 'Eliminar Proveedores', 'description' => 'Puede eliminar proveedores'],

            // Reportes
            ['name' => 'reports.view', 'display_name' => 'Ver Reportes', 'description' => 'Puede acceder a los reportes del sistema'],
            ['name' => 'reports.export', 'display_name' => 'Exportar Reportes', 'description' => 'Puede exportar reportes a PDF/Excel'],

            // Usuarios y Roles
            ['name' => 'users.view', 'display_name' => 'Ver Usuarios', 'description' => 'Puede ver el listado de usuarios'],
            ['name' => 'users.create', 'display_name' => 'Crear Usuarios', 'description' => 'Puede crear nuevos usuarios'],
            ['name' => 'users.edit', 'display_name' => 'Editar Usuarios', 'description' => 'Puede editar usuarios existentes'],
            ['name' => 'users.delete', 'display_name' => 'Eliminar Usuarios', 'description' => 'Puede eliminar usuarios'],
            ['name' => 'roles.manage', 'display_name' => 'Gestionar Roles', 'description' => 'Puede gestionar roles y permisos'],

            // Dashboard
            ['name' => 'dashboard.view', 'display_name' => 'Ver Dashboard', 'description' => 'Puede acceder al dashboard'],

            // TPV (Terminal Punto de Venta)
            ['name' => 'tpv.access', 'display_name' => 'Acceder al TPV', 'description' => 'Puede acceder al Terminal Punto de Venta'],
            ['name' => 'tpv.sell', 'display_name' => 'Realizar Ventas TPV', 'description' => 'Puede realizar ventas desde el TPV'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Crear roles básicos
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrador',
                'description' => 'Acceso total al sistema',
            ]
        );

        $cajeroRole = Role::firstOrCreate(
            ['name' => 'cajero'],
            [
                'display_name' => 'Cajero',
                'description' => 'Puede realizar ventas y gestionar clientes',
            ]
        );

        $gerenteRole = Role::firstOrCreate(
            ['name' => 'gerente'],
            [
                'display_name' => 'Gerente',
                'description' => 'Acceso a ventas, compras, reportes',
            ]
        );

        // Asignar todos los permisos al admin
        $allPermissions = Permission::all();
        $adminRole->permissions()->sync($allPermissions->pluck('id'));

        // Asignar permisos al cajero
        $cajeroPermissions = Permission::whereIn('name', [
            'dashboard.view',
            'tpv.access',
            'tpv.sell',
            'sales.view',
            'sales.create',
            'customers.view',
            'customers.create',
            'customers.edit',
            'payments.view',
            'payments.create',
            'products.view',
        ])->get();
        $cajeroRole->permissions()->sync($cajeroPermissions->pluck('id'));

        // Asignar permisos al gerente
        $gerentePermissions = Permission::whereNotIn('name', [
            'users.create',
            'users.delete',
            'roles.manage',
        ])->get();
        $gerenteRole->permissions()->sync($gerentePermissions->pluck('id'));

        // Asignar rol admin al primer usuario
        $firstUser = User::first();
        if ($firstUser) {
            $firstUser->roles()->syncWithoutDetaching([$adminRole->id]);
        }

        $this->command->info('Permisos y roles creados exitosamente!');
    }
}
