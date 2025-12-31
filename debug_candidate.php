<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'nataliefariz69@gmail.com';
$user = \App\Models\User::where('email', $email)->first();

if (!$user) {
    echo "User $email NOT FOUND\n";
    exit;
}

echo "User Found: {$user->name} | ID: {$user->id} | Role: {$user->role}\n";
echo "Application ID in User Table: " . ($user->application_id ?? 'NULL') . "\n";

if ($user->application_id) {
    $app = \App\Models\Application::find($user->application_id);
    if ($app) {
        echo "Linked Application Found: ID {$app->id} | Email: {$app->email}\n";
    } else {
        echo "Linked Application ID {$user->application_id} NOT FOUND in applications table.\n";
    }
} else {
    // Try to find app by email to see if we can link it
    $app = \App\Models\Application::where('email', $email)->first();
    if ($app) {
        echo "Found Application by Email (ID {$app->id}) but not linked in User table.\n";
        echo "Fixing linkage...\n";
        $user->application_id = $app->id;
        $user->save();
        echo "Linkage Fixed.\n";
    } else {
        echo "No application found for this email.\n";
    }
}
