<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Schema;

echo "Checking columns in users table...\n";
$columns = ['total_credits', 'used_credits', 'last_quota_reset_at'];
foreach ($columns as $column) {
    if (Schema::hasColumn('users', $column)) {
        echo "Column $column exists.\n";
    } else {
        echo "Column $column DOES NOT exist.\n";
    }
}

$user = User::first();
if ($user) {
    echo "Testing User model methods...\n";
    echo "Initial credits: " . $user->remaining_credits . "\n";
    $user->checkAndResetQuota();
    echo "After reset credits: " . $user->remaining_credits . "\n";
    
    $cost = 4;
    if ($user->hasCredits($cost)) {
        echo "User has $cost credits. Deducting...\n";
        $user->deductCredits($cost);
        $user->refresh();
        echo "Remaining credits: " . $user->remaining_credits . "\n";
    } else {
        echo "User DOES NOT have $cost credits.\n";
    }
} else {
    echo "No user found in database.\n";
}
