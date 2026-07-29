<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CompleteSystemSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        DB::table('roles')->insert([
            ['name' => 'admin', 'guard_name' => 'web', 'permissions_json' => json_encode(['all']), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'project_manager', 'guard_name' => 'web', 'permissions_json' => json_encode(['projects', 'sites', 'reports']), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'site_engineer', 'guard_name' => 'web', 'permissions_json' => json_encode(['reports', 'checkins']), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'worker', 'guard_name' => 'web', 'permissions_json' => json_encode(['checkins']), 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Create Admin
        DB::table('users')->insert([
            'uuid' => Str::uuid(),
            'full_name' => 'System Administrator',
            'email' => 'admin@tntconstruction.com',
            'phone_number' => '+251900000000',
            'password' => Hash::make('Admin@123'),
            'employee_id' => 'ADM-001',
            'position' => 'System Administrator',
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Project Manager
        DB::table('users')->insert([
            'uuid' => Str::uuid(),
            'full_name' => 'Abebe Tadesse',
            'email' => 'manager@tntconstruction.com',
            'phone_number' => '+251911111111',
            'password' => Hash::make('password'),
            'employee_id' => 'MGR-001',
            'position' => 'Senior Project Manager',
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Site Engineers
        $engineers = [
            ['Sara Haile', 'engineer1@tntconstruction.com', 'ENG-001', '+251922222221'],
            ['Daniel Worku', 'engineer2@tntconstruction.com', 'ENG-002', '+251922222222'],
        ];

        foreach ($engineers as $engineer) {
            DB::table('users')->insert([
                'uuid' => Str::uuid(),
                'full_name' => $engineer[0],
                'email' => $engineer[1],
                'phone_number' => $engineer[3],
                'password' => Hash::make('password'),
                'employee_id' => $engineer[2],
                'position' => 'Site Engineer',
                'status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create Workers
        for ($i = 1; $i <= 10; $i++) {
            DB::table('users')->insert([
                'uuid' => Str::uuid(),
                'full_name' => "Construction Worker $i",
                'email' => "worker$i@tntconstruction.com",
                'phone_number' => "+2519333333" . sprintf('%02d', $i),
                'password' => Hash::make('password'),
                'employee_id' => "WRK-" . sprintf('%03d', $i),
                'position' => 'Construction Worker',
                'status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $managerId = 2; // Abebe's ID

        // Create 3 Active Projects
        $projects = [
            [
                'name' => 'Ayat Luxury Apartments',
                'code' => 'PRJ-001',
                'location' => 'Ayat, Addis Ababa',
                'status' => 'active',
                'priority' => 'high',
                'start_date' => '2024-01-15',
                'end_date' => '2025-06-30',
                'budget' => 85000000,
                'client_name' => 'Ayat Real Estate SC',
            ],
            [
                'name' => 'Bole Business Center',
                'code' => 'PRJ-002',
                'location' => 'Bole, Addis Ababa',
                'status' => 'active',
                'priority' => 'critical',
                'start_date' => '2024-03-01',
                'end_date' => '2025-12-31',
                'budget' => 120000000,
                'client_name' => 'Bole Developers PLC',
            ],
            [
                'name' => 'CMC Residential Complex',
                'code' => 'PRJ-003',
                'location' => 'CMC, Addis Ababa',
                'status' => 'planning',
                'priority' => 'medium',
                'start_date' => '2024-06-01',
                'end_date' => '2026-03-31',
                'budget' => 95000000,
                'client_name' => 'CMC Housing Association',
            ],
        ];

        foreach ($projects as $project) {
            DB::table('projects')->insert(array_merge($project, [
                'uuid' => Str::uuid(),
                'manager_id' => $managerId,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Create Sites for each project
        $sitesData = [
            ['project_id' => 1, 'name' => 'Building A - Foundation', 'code' => 'SITE-A-01', 'area' => 2500, 'progress' => 45],
            ['project_id' => 1, 'name' => 'Building B - Superstructure', 'code' => 'SITE-B-01', 'area' => 2800, 'progress' => 30],
            ['project_id' => 1, 'name' => 'Utility Works', 'code' => 'SITE-UT-01', 'area' => 1000, 'progress' => 60],
            ['project_id' => 2, 'name' => 'Tower 1 - Main Structure', 'code' => 'SITE-T1-01', 'area' => 3500, 'progress' => 25],
            ['project_id' => 2, 'name' => 'Parking Complex', 'code' => 'SITE-PK-01', 'area' => 5000, 'progress' => 15],
        ];

        foreach ($sitesData as $site) {
            DB::table('sites')->insert([
                'uuid' => Str::uuid(),
                'project_id' => $site['project_id'],
                'site_name' => $site['name'],
                'site_code' => $site['code'],
                'supervisor_id' => 3, // First engineer
                'status' => 'active',
                'type' => 'main_site',
                'area_sqm' => $site['area'],
                'progress_percentage' => $site['progress'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create Daily Reports for last 30 days
        for ($d = 0; $d < 30; $d++) {
            $date = now()->subDays($d);
            if ($date->isWeekend()) continue;

            for ($siteId = 1; $siteId <= 5; $siteId++) {
                DB::table('daily_reports')->insert([
                    'uuid' => Str::uuid(),
                    'site_id' => $siteId,
                    'project_id' => $siteId <= 3 ? 1 : 2,
                    'submitted_by' => $siteId <= 3 ? 3 : 4,
                    'report_date' => $date->format('Y-m-d'),
                    'workforce_count' => rand(30, 80),
                    'subcontractor_count' => rand(5, 20),
                    'absent_count' => rand(0, 5),
                    'progress_percentage' => rand(20, 70),
                    'summary_text' => 'Daily construction progress update. All activities proceeding as planned.',
                    'status' => $d == 0 ? 'submitted' : 'approved',
                    'approved_by' => $d > 0 ? $managerId : null,
                    'approved_at' => $d > 0 ? $date : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        echo "✅ Complete system data seeded!\n";
        echo "Admin: admin@tntconstruction.com / Admin@123\n";
        echo "Manager: manager@tntconstruction.com / password\n";
        echo "Engineers: engineer1@tntconstruction.com / password\n";
        echo "Workers: worker1-10@tntconstruction.com / password\n";
    }
}
