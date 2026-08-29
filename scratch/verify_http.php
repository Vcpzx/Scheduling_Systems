<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$consoleKernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$consoleKernel->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "=== 2. VERIFYING HTTP REQUESTS & AUTH FLOWS ===\n";

$admin = User::where('role', 'admin')->first();
$teacher = User::where('role', 'teacher')->first();
$student = User::where('role', 'student')->first();

function testRoute($kernel, $uri, $user = null) {
    if ($user) {
        Auth::login($user);
    } else {
        Auth::logout();
    }

    $request = Request::create($uri, 'GET');
    if ($user) {
        $request->setUserResolver(fn() => $user);
    }

    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    $content = $response->getContent();

    echo "GET $uri (" . ($user ? $user->role : 'guest') . ") -> HTTP $status\n";

    // Emoji check
    if (preg_match('/[\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1FA00}-\x{1FAFF}]/u', $content, $matches)) {
        echo "  [FAIL] Emoji found in response: " . $matches[0] . "\n";
        exit(1);
    }

    return $response;
}

testRoute($kernel, '/login');
testRoute($kernel, '/register');
testRoute($kernel, '/forgot-password');

testRoute($kernel, '/campus-schedule', $admin);
testRoute($kernel, '/admin', $admin);

testRoute($kernel, '/teacher', $teacher);
testRoute($kernel, '/teacher?tab=semesters', $teacher);
testRoute($kernel, '/teacher?tab=room-requests', $teacher);
testRoute($kernel, '/teacher?tab=notifications', $teacher);

testRoute($kernel, '/student', $student);
testRoute($kernel, '/student?tab=registration', $student);
testRoute($kernel, '/student?tab=notifications', $student);
testRoute($kernel, '/student?tab=campus-schedule', $student);

echo "=== ALL HTTP ENDPOINTS TESTED SUCCESSFULLY WITH ZERO EMOJIS! ===\n";
