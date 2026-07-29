<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating sample data...');

        // Create admin
        DB::table('users')->insert([
            'uuid' => Str::uuid(),
            'full_name' => 'System Admin',
            'email' => 'admin@tntconstruction.com',
            'phone_number' => '+251900000000',
            'password' => Hash::make('Admin@123'),
            'employee_id' => 'EMP-000',
            'position' => 'Administrator',
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create manager
        DB::table('users')->insert([
            'uuid' => Str::uuid(),
            'full_name' => 'Abebe Tadesse',
            'email' => 'manager@tntconstruction.com',
            'phone_number' => '+251911111111',
            'password' => Hash::make('password'),
            'employee_id' => 'EMP-100',
            'position' => 'Project Manager',
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create engineer
        DB::table('users')->insert([
            'uuid' => Str::uuid(),
            'full_name' => 'Sara Haile',
            'email' => 'engineer@tntconstruction.com',
            'phone_number' => '+251922222222',
            'password' => Hash::make('password'),
            'employee_id' => 'EMP-200',
            'position' => 'Site Engineer',
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create workers with unique IDs
        $workerNames = [
            ['Daniel Worku', 'EMP-301'],
            ['Meron Alemu', 'EMP-302'],
            ['Yonas Girma', 'EMP-303'],
            ['Tigist Mengistu', 'EMP-304'],
            ['Dawit Hailu', 'EMP-305'],
        ];

        foreach ($workerNames as $index => $worker) {
            DB::table('users')->insert([
                'uuid' => Str::uuid(),
                'full_name' => $worker[0],
                'email' => strtolower(str_replace(' ', '.', $worker[0])) . '@tntconstruction.com',
                'phone_number' => '+2519333333' . ($index + 1),
                'password' => Hash::make('password'),
                'employee_id' => $worker[1],
                'status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Get user IDs
        $managerId = DB::table('users')->where('email', 'manager@tntconstruction.com')->value('id');
        $engineerId = DB::table('users')->where('email', 'engineer@tntconstruction.com')->value('id');

        // Create projects
        DB::table('projects')->insert([
            [
                'uuid' => Str::uuid(),
                'name' => 'Ayat Luxury Apartments',
                'code' => 'PRJ-001',
                'location' => 'Ayat, Addis Ababa',
                'manager_id' => $managerId,
                'status' => 'active',
                'priority' => 'high',
                'start_date' => '2024-01-15',
                'end_date' => '2025-06-30',
                'budget' => 85000000,
                'client_name' => 'Ayat Real Estate SC',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Bole Business Center',
                'code' => 'PRJ-002',
                'location' => 'Bole, Addis Ababa',
                'manager_id' => $managerId,
                'status' => 'active',
                'priority' => 'critical',
                'start_date' => '2024-03-01',
                'end_date' => '2025-12-31',
                'budget' => 120000000,
                'client_name' => 'Bole Developers PLC',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $projectId = DB::table('projects')->where('code', 'PRJ-001')->value('id');
        $projectId2 = DB::table('projects')->where('code', 'PRJ-002')->value('id');

        // Create sites
        DB::table('sites')->insert([
            [
                'uuid' => Str::uuid(),
                'project_id' => $projectId,
                'site_name' => 'Building A - Main Structure',
                'site_code' => 'SITE-A-01',
                'supervisor_id' => $engineerId,
                'status' => 'active',
                'type' => 'main_site',
                'area_sqm' => 2500,
                'progress_percentage' => 45,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'project_id' => $projectId,
                'site_name' => 'Building B - Foundation',
                'site_code' => 'SITE-B-01',
                'supervisor_id' => $engineerId,
                'status' => 'active',
                'type' => 'main_site',
                'area_sqm' => 2800,
                'progress_percentage' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'project_id' => $projectId2,
                'site_name' => 'Tower 1 - Main Structure',
                'site_code' => 'SITE-T1-01',
                'supervisor_id' => $engineerId,
                'status' => 'active',
                'type' => 'main_site',
                'area_sqm' => 3500,
                'progress_percentage' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $site1Id = DB::table('sites')->where('site_code', 'SITE-A-01')->value('id');
        $site2Id = DB::table('sites')->where('site_code', 'SITE-B-01')->value('id');
        $site3Id = DB::table('sites')->where('site_code', 'SITE-T1-01')->value('id');

        // Create daily reports
        $sites = [$site1Id => $projectId, $site2Id => $projectId, $site3Id => $projectId2];
        for ($d = 0; $d < 7; $d++) {
            $date = now()->subDays($d);
            if ($date->isWeekend()) continue;

            foreach ($sites as $siteId => $projId) {
                DB::table('daily_reports')->insert([
                    'uuid' => Str::uuid(),
                    'site_id' => $siteId,
                    'project_id' => $projId,
                    'submitted_by' => $engineerId,
                    'report_date' => $date->format('Y-m-d'),
                    'workforce_count' => rand(40, 75),
                    'subcontractor_count' => rand(5, 15),
                    'progress_percentage' => rand(20, 50),
                    'summary_text' => 'Daily construction progress update.',
                    'status' => $d == 0 ? 'submitted' : 'approved',
                    'approved_by' => $d > 0 ? $managerId : null,
                    'approved_at' => $d > 0 ? $date : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Create check-ins
        $workerIds = DB::table('users')->where('employee_id', 'like', 'EMP-30%')->pluck('id');
        foreach ($workerIds as $workerId) {
            for ($d = 0; $d < 5; $d++) {
                $date = now()->subDays($d);
                if ($date->isWeekend()) continue;

                DB::table('worker_checkins')->insert([
                    'uuid' => Str::uuid(),
                    'user_id' => $workerId,
                    'site_id' => collect([$site1Id, $site2Id, $site3Id])->random(),
                    'check_in_time' => $date->copy()->setHour(7)->setMinute(rand(0, 30)),
                    'check_out_time' => $date->copy()->setHour(17)->setMinute(rand(0, 30)),
                    'check_in_method' => 'mobile_app',
                    'status' => 'checked_out',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Sample data created!');
        $this->command->info('Admin: admin@tntconstruction.com / Admin@123');
        $this->command->info('Manager: manager@tntconstruction.com / password');
        $this->command->info('Engineer: engineer@tntconstruction.com / password');
    }
}
