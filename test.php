<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$workflows = \App\Models\Workflow::with('steps')->get();
foreach ($workflows as $w) {
    echo "ID: {$w->id}, Name: {$w->name}, Steps: " . count($w->steps) . "\n";
}
