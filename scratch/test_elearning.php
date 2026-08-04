<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

view()->share('errors', new \Illuminate\Support\ViewErrorBag());

$roles = ['superadmin', 'admin', 'guru', 'siswa'];
foreach ($roles as $role) {
    $user = \App\Models\User::where('role', $role)->first();
    if (!$user) {
        echo "Role {$role}: No user found\n";
        continue;
    }
    auth()->login($user);
    try {
        $controller = new \App\Http\Controllers\DashboardController(
            app(\App\Services\AttendanceService::class),
            app(\App\Services\DashboardService::class)
        );
        $view = $controller->elearning();
        $html = $view->render();
        echo "Role {$role}: OK (" . strlen($html) . " bytes)\n";
    } catch (\Throwable $e) {
        echo "Role {$role} ERROR: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
}
