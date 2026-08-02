<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::with('student')->where('username', '0107658733')->first();

if ($user) {
    echo "Username: {$user->username}\n";
    echo "Name: {$user->name}\n";
    echo "Student DOB: " . ($user->student->dob ? $user->student->dob->format('Y-m-d') : 'null') . "\n";
    echo "Check Password ('2010-05-01'): " . (Hash::check('2010-05-01', $user->password) ? 'MATCH' : 'NO MATCH') . "\n";
    echo "Needs Password Change: " . ($user->needsPasswordChange() ? 'YES (FORCED TO CHANGE)' : 'NO') . "\n";
} else {
    echo "User 0107658733 not found.\n";
}
