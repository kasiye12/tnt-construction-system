<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Site;
use App\Models\DailyReport;
use App\Models\WorkerCheckin;
use App\Models\Channel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating sample data...');

        // Create Users
        $users = $this->createUsers();
        
        // Create Projects
        $projects = $this->createProjects($users['manager']->id);
        
        // Create Sites
        $sites = $this->createSites($projects, $users['engineer']->id);
        
        // Create Daily Reports
        $this->createDailyReports($sites, $users);
        
        // Create Check-ins
        $this->createCheckins($sites, $users);
        
        // Create Chat Channels
        $this->createChannels($projects);

        $this->command->info('Sample data created successfully!');
        $this->command->info('Login: admin@tntconstruction.com / Admin@123');
    }

    private function createUsers()
    {
        // Manager
        $manager = User::create([
            'uuid' => Str::uuid(),
            'full_name' => 'Abebe Tadesse',
            'email' => 'abebe@tntconstruction.com',
            'phone_number' => '+251911111111',
            'password' => Hash::make('password'),
            'employee_id' => 'EMP-001',
            'department' => 'Project Management',
            'position' => 'Senior Project Manager',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $manager->assignRole('project_manager');

        // Site Engineer 1
        $engineer = User::create([
            'uuid' => Str::uuid(),
            'full_name' => 'Sara Haile',
            'email' => 'sara@tntconstruction.com',
            'phone_number' => '+251922222222',
            'password' => Hash::make('password'),
            'employee_id' => 'EMP-002',
            'department' => 'Engineering',
            'position' => 'Site Engineer',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $engineer->assignRole('site_engineer');

        // Workers
        $workers = [];
        $workerNames = [
            'Daniel Worku', 'Meron Alemu', 'Yonas Girma', 
            'Tigist Mengistu', 'Dawit Hailu', 'Hanna Bekele',
            'Fikru Tadesse', 'Betelhem Kebede', 'Samuel Tekle'
        ];

        foreach ($workerNames as $index => $name) {
            $worker = User::create([
                'uuid' => Str::uuid(),
                'full_name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@tntconstruction.com',
                'phone_number' => '+2519333333' . sprintf('%02d', $index),
                'password' => Hash::make('password'),
                'employee_id' => 'EMP-' . sprintf('%03d', $index + 3),
                'department' => 'Construction',
                'position' => 'Construction Worker',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
            $worker->assignRole('worker');
            $workers[] = $worker;
        }

        return [
            'manager' => $manager,
            'engineer' => $engineer,
            'workers' => $workers,
        ];
    }

    private function createProjects($managerId)
    {
        $projects = [];
        
        $projectData = [
            [
                'name' => 'Ayat Luxury Apartments',
                'code' => 'PRJ-AYAT-001',
                'location' => 'Ayat, Addis Ababa',
                'status' => 'active',
                'priority' => 'high',
                'start_date' => '2024-01-15',
                'end_date' => '2025-06-30',
                'budget' => 85000000.00,
                'client_name' => 'Ayat Real Estate SC',
                'client_contact' => '+251911234567',
                'description' => 'Construction of 5 luxury apartment buildings with 12 floors each.',
            ],
            [
                'name' => 'Bole Business Center',
                'code' => 'PRJ-BOLE-002',
                'location' => 'Bole, Addis Ababa',
                'status' => 'active',
                'priority' => 'critical',
                'start_date' => '2024-03-01',
                'end_date' => '2025-12-31',
                'budget' => 120000000.00,
                'client_name' => 'Bole Developers PLC',
                'client_contact' => '+251922334455',
                'description' => 'Modern business center with office spaces and commercial areas.',
            ],
            [
                'name' => 'CMC Residential Complex',
                'code' => 'PRJ-CMC-003',
                'location' => 'CMC, Addis Ababa',
                'status' => 'planning',
                'priority' => 'medium',
                'start_date' => '2024-06-01',
                'end_date' => '2026-03-31',
                'budget' => 95000000.00,
                'client_name' => 'CMC Housing Association',
                'client_contact' => '+251933445566',
                'description' => 'Residential complex with 200 housing units and amenities.',
            ],
        ];

        foreach ($projectData as $data) {
            $data['uuid'] = Str::uuid();
            $data['manager_id'] = $managerId;
            $projects[] = Project::create($data);
        }

        return $projects;
    }

    private function createSites($projects, $engineerId)
    {
        $sites = [];
        
        $siteData = [
            // Project 1 Sites
            [
                'project_id' => $projects[0]->id,
                'site_name' => 'Building A - Foundation',
                'site_code' => 'SITE-A-001',
                'status' => 'active',
                'type' => 'main_site',
                'address' => 'Ayat Zone 3, Block 12',
                'area_sqm' => 2500,
                'start_date' => '2024-01-20',
                'expected_end_date' => '2024-08-30',
                'max_workers' => 80,
            ],
            [
                'project_id' => $projects[0]->id,
                'site_name' => 'Building B - Superstructure',
                'site_code' => 'SITE-B-001',
                'status' => 'active',
                'type' => 'main_site',
                'address' => 'Ayat Zone 3, Block 13',
                'area_sqm' => 2800,
                'start_date' => '2024-02-15',
                'expected_end_date' => '2024-10-15',
                'max_workers' => 90,
            ],
            [
                'project_id' => $projects[0]->id,
                'site_name' => 'Utility Works',
                'site_code' => 'SITE-UTIL-001',
                'status' => 'active',
                'type' => 'sub_site',
                'address' => 'Ayat Zone 3, Service Area',
                'area_sqm' => 1000,
                'start_date' => '2024-03-01',
                'expected_end_date' => '2024-09-30',
                'max_workers' => 40,
            ],
            // Project 2 Sites
            [
                'project_id' => $projects[1]->id,
                'site_name' => 'Tower 1 - Main Structure',
                'site_code' => 'SITE-BOLE-T1',
                'status' => 'active',
                'type' => 'main_site',
                'address' => 'Bole Sub-city, Woreda 5',
                'area_sqm' => 3500,
                'start_date' => '2024-03-15',
                'expected_end_date' => '2025-06-30',
                'max_workers' => 120,
            ],
            [
                'project_id' => $projects[1]->id,
                'site_name' => 'Parking Complex',
                'site_code' => 'SITE-BOLE-PK',
                'status' => 'active',
                'type' => 'sub_site',
                'address' => 'Bole Sub-city, Woreda 5, Basement',
                'area_sqm' => 5000,
                'start_date' => '2024-04-01',
                'expected_end_date' => '2024-12-31',
                'max_workers' => 60,
            ],
        ];

        foreach ($siteData as $data) {
            $data['uuid'] = Str::uuid();
            $data['supervisor_id'] = $engineerId;
            $sites[] = Site::create($data);
        }

        return $sites;
    }

    private function createDailyReports($sites, $users)
    {
        $reports = [];
        
        // Generate 10 days of reports
        for ($i = 0; $i < 10; $i++) {
            $date = now()->subDays($i);
            
            // Skip weekends
            if ($date->isWeekend()) continue;

            foreach (array_slice($sites, 0, 3) as $index => $site) {
                $progressBase = 35 + ($index * 5) + ((10 - $i) * 2);
                $workforce = rand(30, 75);
                
                $report = DailyReport::create([
                    'uuid' => Str::uuid(),
                    'site_id' => $site->id,
                    'project_id' => $site->project_id,
                    'submitted_by' => $users['engineer']->id,
                    'report_date' => $date,
                    'workforce_count' => $workforce,
                    'subcontractor_count' => rand(5, 20),
                    'absent_count' => rand(0, 5),
                    'progress_percentage' => min($progressBase, 100),
                    'summary_text' => $this->getRandomSummary(),
                    'challenges_encountered' => $this->getRandomChallenge(),
                    'safety_incidents' => rand(0, 3) == 0 ? 'Minor incident reported - first aid administered' : null,
                    'material_deliveries' => 'Cement: ' . rand(20, 50) . ' bags, Steel: ' . rand(1, 5) . ' tons',
                    'status' => $i == 0 ? 'submitted' : 'approved',
                    'approved_by' => $i > 0 ? $users['manager']->id : null,
                    'approved_at' => $i > 0 ? $date->addHours(rand(2, 6)) : null,
                ]);
                
                $reports[] = $report;
            }
        }

        return $reports;
    }

    private function createCheckins($sites, $users)
    {
        $checkins = [];
        
        // Create check-ins for last 7 days
        foreach ($users['workers'] as $worker) {
            for ($i = 0; $i < 7; $i++) {
                $date = now()->subDays($i);
                if ($date->isWeekend()) continue;
                
                $site = $sites[array_rand(array_slice($sites, 0, 3))];
                $checkInTime = $date->copy()->setHour(rand(7, 8))->setMinute(rand(0, 59));
                
                $checkin = WorkerCheckin::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $worker->id,
                    'site_id' => $site->id,
                    'check_in_time' => $checkInTime,
                    'check_out_time' => $checkInTime->copy()->addHours(rand(8, 10)),
                    'check_in_latitude' => 9.0222 + (rand(0, 100) / 10000),
                    'check_in_longitude' => 38.7468 + (rand(0, 100) / 10000),
                    'check_in_method' => 'mobile_app',
                    'status' => 'checked_out',
                    'hours_worked' => rand(8, 10),
                ]);
                
                $checkins[] = $checkin;
            }
        }

        return $checkins;
    }

    private function createChannels($projects)
    {
        $channels = [];
        
        foreach ($projects as $project) {
            $channel = Channel::create([
                'uuid' => Str::uuid(),
                'project_id' => $project->id,
                'name' => $project->name . ' - General',
                'type' => 'project',
                'description' => 'General discussion for ' . $project->name,
                'created_by' => 1,
            ]);
            
            $channels[] = $channel;
        }

        // Create a company-wide announcement channel
        $channels[] = Channel::create([
            'uuid' => Str::uuid(),
            'name' => 'TNT Company Announcements',
            'type' => 'announcement',
            'description' => 'Official company announcements and updates',
            'created_by' => 1,
        ]);

        return $channels;
    }

    private function getRandomSummary()
    {
        $summaries = [
            'Completed foundation work for main columns. Started rebar installation for ground floor.',
            'Concrete pouring for first floor slab completed. Curing in progress.',
            'Electrical conduit installation in progress. Plumbing rough-in completed.',
            'Brickwork for ground floor completed. Started plastering work.',
            'Steel frame erection for third floor. Welding inspections passed.',
            'Window frame installation started. Waterproofing of basement completed.',
            'HVAC ductwork installation ongoing. Fire sprinkler system tested successfully.',
            'Roof truss installation completed. Started roofing sheets installation.',
            'Floor tiling work in progress. Elevator shaft construction completed.',
            'Site cleanup and preparation for next phase. Material inventory updated.',
        ];
        
        return $summaries[array_rand($summaries)];
    }

    private function getRandomChallenge()
    {
        $challenges = [
            'Minor delay due to rain for 2 hours in the morning.',
            'Concrete pump malfunction - resolved within 1 hour.',
            'Material delivery delayed - using backup stock.',
            'Generator maintenance required - temporary power interruption.',
            'None - work progressing as planned.',
            'Welding machine repair needed - spare unit deployed.',
        ];
        
        return $challenges[array_rand($challenges)];
    }
}
