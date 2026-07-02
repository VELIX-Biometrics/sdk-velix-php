#!/usr/bin/env php
<?php
declare(strict_types=1);
if (!is_dir(__DIR__ . '/vendor')) {
    passthru('composer install --no-interaction --quiet', $code);
    if ($code !== 0) { fwrite(STDERR, "composer install falhou\n"); exit(1); }
}
require __DIR__ . '/vendor/autoload.php';

use Velix\VelixClient;
use Velix\Modules\OnboardingModule;
use Velix\Modules\CheckinModule;
use Velix\Modules\LgpdModule;
use Velix\Modules\MeModule;
use Velix\Modules\EventsModule;

const IMG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

function result(string $step, bool $ok, string $detail): void {
    echo "RESULT:php:$step:" . ($ok ? 'PASS' : 'FAIL') . ":$detail\n";
}

function reachable(string $msg): bool {
    $m = strtolower($msg);
    foreach (['route not found', 'no route', '401', '403'] as $s) {
        if (str_contains($m, $s)) return false;
    }
    return true;
}

$client = new VelixClient([
    'apiUrl' => getenv('API_BASE_URL'),
    'apiKey' => getenv('VELIX_API_KEY'),
]);

$onboarding = new OnboardingModule($client);
$checkin = new CheckinModule($client);
$lgpd = new LgpdModule($client);
$me = new MeModule($client);
$events = new EventsModule($client);

$personId = null;
try {
    $r = $onboarding->enroll('Smoke Test PHP', [IMG, IMG, IMG]);
    $personId = $r->personId;
    result('onboarding', $personId !== null, "person_id=$personId");
} catch (\Throwable $e) {
    result('onboarding', false, $e->getMessage());
}

try {
    $r = $checkin->identify(IMG);
    result('checkin', true, 'matched=' . var_export($r->matched, true));
} catch (\Throwable $e) {
    result('checkin', false, $e->getMessage());
}

if ($personId !== null) {
    try {
        $lgpd->requestDeletion($personId);
        result('lgpd', true, 'deletion-request ok');
    } catch (\Throwable $e) {
        result('lgpd', false, $e->getMessage());
    }
    try {
        $me->get($personId);
        result('me', true, 'got response');
    } catch (\Throwable $e) {
        result('me', false, $e->getMessage());
    }
}

$dummy = '00000000-0000-0000-0000-000000000000';
try {
    $events->createGuest($dummy, ['name' => 'Guest Smoke', 'email' => 'guest@smoke.test']);
    result('events_create', true, 'endpoint reachable');
} catch (\Throwable $e) {
    result('events_create', reachable($e->getMessage()), $e->getMessage());
}

try {
    $events->getGuest($dummy, $dummy);
    result('events_get', true, 'endpoint reachable');
} catch (\Throwable $e) {
    result('events_get', reachable($e->getMessage()), $e->getMessage());
}
