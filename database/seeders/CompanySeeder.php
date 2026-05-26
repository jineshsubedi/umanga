<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use App\Models\MeetingMemo;
use App\Models\MeetingMemoReview;
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

            // Create Staffs
            $staffs = [];
            for ($k = 1; $k <= 5; $k++) {
                $staffs[] = User::create([
                    'company_id' => $company->id,
                    'name' => "Company $i Staff $k",
                    'email' => "staff$k@company$i.com",
                    'password' => $password,
                    'role' => 'staff',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]);
            }

            // Create Memos
            $statuses = ['draft', 'pending_manager', 'pending_admin', 'approved', 'rejected'];
            foreach ($staffs as $staff) {
                // Each staff creates 3 memos
                for ($m = 1; $m <= 3; $m++) {
                    $status = $statuses[array_rand($statuses)];
                    
                    $memo = MeetingMemo::create([
                        'company_id' => $company->id,
                        'created_by' => $staff->id,
                        'title' => "Memo $m by {$staff->name}",
                        'content' => "<p>This is the detailed content for meeting memo $m.</p><ul><li>Discussed project roadmap</li><li>Assigned tasks to team members</li><li>Set next meeting date</li></ul>",
                        'meeting_date' => now()->subDays(rand(1, 30)),
                        'status' => $status,
                    ]);

                    $manager = $managers[array_rand($managers)];

                    // Add manager review if status passed pending_manager
                    if (in_array($status, ['pending_admin', 'approved'])) {
                        MeetingMemoReview::create([
                            'meeting_memo_id' => $memo->id,
                            'reviewed_by' => $manager->id,
                            'status' => 'approved',
                            'comment' => "Approved by manager {$manager->name} and forwarded to admin.",
                        ]);
                    } elseif ($status === 'rejected') {
                        MeetingMemoReview::create([
                            'meeting_memo_id' => $memo->id,
                            'reviewed_by' => $manager->id,
                            'status' => 'rejected',
                            'comment' => "Rejected by manager {$manager->name}.",
                        ]);
                    }

                    // Add admin review if approved
                    if ($status === 'approved') {
                        MeetingMemoReview::create([
                            'meeting_memo_id' => $memo->id,
                            'reviewed_by' => $admin->id,
                            'status' => 'approved',
                            'comment' => "Final approval by admin {$admin->name}.",
                        ]);
                    }
                }
            }
        }
    }
}
