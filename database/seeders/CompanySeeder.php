<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use App\Models\MeetingMinute;
use App\Models\MeetingMinuteReview;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        for ($i = 1; $i <= 3; $i++) {
            $company = Company::create([
                'name' => "Company $i",
                'email' => "contact@company$i.com",
                'phone' => "1234567890$i",
                'address' => "123 Company $i St, Business City",
                'status' => 'active',
            ]);

            // Create Company Admin
            $admin = User::create([
                'company_id' => $company->id,
                'name' => "Company $i Admin",
                'email' => "admin@company$i.com",
                'password' => $password,
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            // Create Managers
            $managers = [];
            for ($j = 1; $j <= 2; $j++) {
                $managers[] = User::create([
                    'company_id' => $company->id,
                    'name' => "Company $i Manager $j",
                    'email' => "manager$j@company$i.com",
                    'password' => $password,
                    'role' => 'manager',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]);
            }

            // Create Clients
            $clients = [];
            for ($k = 1; $k <= 5; $k++) {
                $clients[] = User::create([
                    'company_id' => $company->id,
                    'name' => "Company $i Client $k",
                    'email' => "client$k@company$i.com",
                    'password' => $password,
                    'role' => 'client',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]);
            }

            // Create Meeting Minutes
            $statuses = ['draft', 'pending', 'approved', 'rejected'];
            foreach ($clients as $client) {
                // Each client creates 3 meeting minutes
                for ($m = 1; $m <= 3; $m++) {
                    $status = $statuses[array_rand($statuses)];
                    
                    $minute = MeetingMinute::create([
                        'company_id' => $company->id,
                        'created_by' => $client->id,
                        'title' => "Meeting Minute $m by {$client->name}",
                        'content' => "<p>This is the detailed content for meeting minute $m.</p><ul><li>Discussed project roadmap</li><li>Assigned tasks to team members</li><li>Set next meeting date</li></ul>",
                        'meeting_date' => now()->subDays(rand(1, 30)),
                        'status' => $status,
                    ]);

                    // Add a review if it's approved or rejected
                    if ($status === 'approved' || $status === 'rejected') {
                        $manager = $managers[array_rand($managers)];
                        MeetingMinuteReview::create([
                            'meeting_minute_id' => $minute->id,
                            'reviewed_by' => $manager->id,
                            'status' => $status,
                            'comment' => "This minute has been $status by {$manager->name}.",
                        ]);
                    }
                }
            }
        }
    }
}
