<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'employee.view', 'employee.create', 'employee.edit', 'employee.delete',
            'attendance.view', 'attendance.checkin', 'attendance.checkout',
            'attendance.view.own', 'attendance.correction',
            'leave.create', 'leave.view.own', 'leave.approve',
            'overtime.create', 'overtime.view.own', 'overtime.approve',
            'report.attendance', 'report.leave', 'report.export',
            'division.view', 'division.create', 'division.edit', 'division.delete',
            'position.view', 'position.create', 'position.edit', 'position.delete',
            'shift.view', 'shift.create', 'shift.edit', 'shift.delete',
            'location.view', 'location.create', 'location.edit', 'location.delete',
            'holiday.view', 'holiday.create', 'holiday.edit', 'holiday.delete',
            'leavetype.view', 'leavetype.create', 'leavetype.edit', 'leavetype.delete',
            'approvalchain.view', 'approvalchain.create', 'approvalchain.edit', 'approvalchain.delete',
            'settings.view', 'settings.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        $superAdmin = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        $hrd = Role::create(['name' => 'HRD', 'guard_name' => 'web']);
        $hrd->givePermissionTo([
            'employee.view', 'employee.create', 'employee.edit', 'employee.delete',
            'attendance.view', 'attendance.correction',
            'report.attendance', 'report.leave', 'report.export',
            'division.view', 'division.create', 'division.edit',
            'position.view', 'position.create', 'position.edit',
            'shift.view', 'shift.create', 'shift.edit',
            'location.view', 'location.create', 'location.edit',
            'holiday.view', 'holiday.create', 'holiday.edit',
            'leavetype.view', 'leavetype.create', 'leavetype.edit',
            'approvalchain.view', 'approvalchain.create', 'approvalchain.edit',
            'settings.view', 'settings.edit',
        ]);

        $manager = Role::create(['name' => 'Manager', 'guard_name' => 'web']);
        $manager->givePermissionTo([
            'attendance.view', 'leave.approve', 'overtime.approve',
        ]);

        $karyawan = Role::create(['name' => 'Karyawan', 'guard_name' => 'web']);
        $karyawan->givePermissionTo([
            'attendance.checkin', 'attendance.checkout', 'attendance.view.own',
            'attendance.correction',
            'leave.create', 'leave.view.own',
            'overtime.create', 'overtime.view.own',
        ]);
    }
}
