<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$memo = \App\Models\MeetingMemo::where('title', 'like', '%Improvised%')->latest()->first();
if (!$memo) { echo "Memo not found\n"; exit; }
echo "Memo: {$memo->title}, status: {$memo->status}, workflow_id: {$memo->workflow_id}, current_step_id: {$memo->current_step_id}\n";
if ($memo->current_step_id) {
    $step = \App\Models\WorkflowStep::find($memo->current_step_id);
    echo "Current Step: type={$step->approver_type}, value={$step->approver_value}\n";
}
$manager = \App\Models\User::where('email', 'manager1@company1.com')->first();
if ($manager) {
    echo "Manager ID: {$manager->id}\n";
    $svc = new \App\Services\WorkflowService();
    $can = $svc->canUserApprove($manager, $memo);
    echo "Can manager1 approve: " . ($can ? 'YES' : 'NO') . "\n";
}
