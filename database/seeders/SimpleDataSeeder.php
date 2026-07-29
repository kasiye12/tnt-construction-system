<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SimpleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create users manually
        $managerId = DB::table('users')->insertGetId([
            'uuid' => Str::uuid(),
            'full_name' => 'Abebe Tadesse',
            'email' => 'abebe@tntconstruction.com',
            'phone_number' => '+251911111111',
            'password' => Hash::make('password'),
            'employee_id' => 'EMP-001',
            'position' => 'Project Manager',
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $engineerId = DB::table('users')->insertGetId([
            'uuid' => Str::uuid(),
            'full_name' => 'Sara Haile',
            'email' => 'sara@tntconstruction.com',
            'phone_number' => '+251922222222',
            'password' => Hash::make('password'),
            'employee_id' => 'EMP-002',
            'position' => 'Site Engineer',
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create project
        $projectId = DB::table('projects')->insertGetId([
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
        ]);

        $projectId2 = DB::table('projects')->insertGetId([
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
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create sites
        $siteId1 = DB::table('sites')->insertGetId([
            'uuid' => Str::uuid(),
            'project_id' => $projectId,
            'site_name' => 'Building A - Main Structure',
            'site_code' => 'SITE-001',
            'supervisor_id' => $engineerId,
            'status' => 'active',
            'type' => 'main_site',
            'area_sqm' => 2500,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $siteId2 = DB::table('sites')->insertGetId([
            'uuid' => Str::uuid(),
            'project_id' => $projectId,
            'site_name' => 'Building B - Foundation',
            'site_code' => 'SITE-002',
            'supervisor_id' => $engineerId,
            'status' => 'active',
            'type' => 'main_site',
            'area_sqm' => 2800,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $siteId3 = DB::table('sites')->insertGetId([
            'uuid' => Str::uuid(),
            'project_id' => $projectId2,
            'site_name' => 'Tower 1 - Main Structure',
            'site_code' => 'SITE-003',
            'supervisor_id' => $engineerId,
            'status' => 'active',
            'type' => 'main_site',
            'area_sqm' => 3500,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create daily reports
        $sites = [$siteId1, $siteId2, $siteId3];
        for($d = 0; $d < 7; $d++) {
            $date = now()->subDays($d);
            if($date->isWeekend()) continue;
            
            foreach($sites as $siteId) {
                DB::table('daily_reports')->insert([
                    'uuid' => Str::uuid(),
                    'site_id' => $siteId,
                    'project_id' => $siteId == $siteId3 ? $projectId2 : $projectId,
                    'submitted_by' => $engineerId,
                    'report_date' => $date,
                    'workforce_count' => rand(40, 75),
                    'progress_percentage' => 30 + ($d * 2),
                    'summary_text' => 'Daily construction progress update',
                    'status' => $d == 0 ? 'submitted' : 'approved',
                    'approved_by' => $d > 0 ? $managerId : null,
                    'approved_at' => $d > 0 ? $date : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        echo "✅ Sample data created!\n";
        echo "Manager: abebe@tntconstruction.com / password\n";
        echo "Engineer: sara@tntconstruction.com / password\n";
    }
}
