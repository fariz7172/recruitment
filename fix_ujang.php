<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'ujang@gmail.com';
$user = \App\Models\User::where('email', $email)->first();
$app = \App\Models\Application::where('email', $email)->first();

if ($app) {
    echo "Application found for $email (ID: {$app->id})\n";
    if ($user) {
        echo "User found (ID: {$user->id})\n";
        echo "Current Role: " . ($user->role ?? 'NULL') . "\n";
        echo "Current App ID: " . ($user->application_id ?? 'NULL') . "\n";
        
        // Fix data
        $user->role = 'candidate';
        $user->application_id = $app->id;
        $user->save();
        
        echo "FIXED: User updated to role 'candidate' and linked to App ID {$app->id}.\n";
    } else {
        echo "User not found. Creating...\n";
        $u = \App\Models\User::create([
            'name' => $app->applicant_name,
            'email' => $app->email,
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'candidate',
            'application_id' => $app->id,
        ]);
        echo "CREATED: User ID {$u->id} with role 'candidate' and App ID {$app->id}.\n";
    }
} else {
    echo "No application found for $email.\n";
}

// Allow mass assignment check for future
$testUser = new \App\Models\User(['role' => 'test', 'application_id' => 999]);
if ($testUser->role === 'test' && $testUser->application_id === 999) {
    echo "Fillable check: PASSED (role and application_id are fillable)\n";
} else {
    echo "Fillable check: FAILED (role/application_id blocked)\n";
}
