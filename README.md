# velix/sdk — PHP SDK ![version](https://img.shields.io/badge/version-0.1.0--alpha1-blue)

> ⚠️ **Alpha / pre-release.** This SDK targets a public API surface that does not yet fully exist on the VELIX backend (see internal task #593). Endpoints and auth may not work against production. Do not use in production integrations yet.

Official PHP SDK for the VELIX Biometrics platform — facial access control B2B SaaS.

## Requirements

- PHP 8.1+
- ext-json
- Composer

## Installation

```bash
composer require velix/sdk
```

## Quick Start

```php
use Velix\VelixClient;
use Velix\Modules\CheckinModule;

$client = new VelixClient([
    'apiUrl' => $_ENV['VELIX_API_URL'],
    'apiKey' => $_ENV['VELIX_API_KEY'],
]);

$result = (new CheckinModule($client))->facial('tenant-slug', $frameBase64);
echo $result->passed ? 'GRANTED' : 'DENIED';
```

## Environment Variables

| Variable | Required | Description |
|----------|----------|-------------|
| `VELIX_API_URL` | Yes | API base URL (`https://api.velixbiometrics.com`) |
| `VELIX_API_KEY` | Yes | Tenant API key (`vx_live_...` or `vx_sandbox_...`) |

## Modules

| Module | Methods |
|--------|---------|
| `CheckinModule` | `facial()`, `qr()`, `pin()`, `getHistory()` |
| `PersonsModule` | `list()`, `get()`, `create()`, `update()`, `delete()`, `enroll()` |
| `EventsModule` | `list()`, `get()`, `create()`, `configure()` |
| `TenantsModule` | `me()`, `updateSettings()` |
| `WebhooksModule` | `configure()`, `validateSignature()` |

## Checkin Module

```php
use Velix\Modules\CheckinModule;

$checkin = new CheckinModule($client);

// Facial identification (base64 JPEG frame)
$result = $checkin->facial('tenant-slug', $frameBase64);
// $result->passed === true
// $result->personId === 'uuid'
// $result->personName === 'João Silva'

// QR code checkin
$result = $checkin->qr('tenant-slug', $qrToken);

// PIN checkin
$result = $checkin->pin('tenant-slug', $pin);

// Paginated history
$history = $checkin->getHistory('tenant-slug', page: 1, limit: 20);
```

## Persons Module

```php
use Velix\Modules\PersonsModule;

$persons = new PersonsModule($client);

// List with optional search
$list = $persons->list(page: 1, limit: 20, search: 'João');

// Get by ID
$person = $persons->get('uuid');

// Create
$created = $persons->create([
    'name'       => 'João Silva',
    'email'      => 'joao@company.com',
    'externalId' => 'EMP-001',
]);

// Update
$persons->update('uuid', ['name' => 'João B. Silva']);

// Enroll biometrics (minimum 3 base64 frames)
$persons->enroll('uuid', [$frame1, $frame2, $frame3]);

// Delete
$persons->delete('uuid');
```

## Events Module

```php
use Velix\Modules\EventsModule;

$events = new EventsModule($client);

$list    = $events->list(page: 1, limit: 20);
$event   = $events->get('uuid');
$created = $events->create(['name' => 'Annual Conference 2026', 'date' => '2026-09-01']);
$events->configure('uuid', ['checkInOpen' => true, 'requireLiveness' => true]);
```

## Webhook Validation

```php
use Velix\Modules\WebhooksModule;

$valid = WebhooksModule::validateSignature(
    payload:   file_get_contents('php://input'),
    signature: $_SERVER['HTTP_X_VELIX_SIGNATURE'],
    secret:    $_ENV['VELIX_WEBHOOK_SECRET']
);

if (!$valid) {
    http_response_code(401);
    exit;
}
```

## Error Handling

```php
use Velix\Exceptions\AuthException;
use Velix\Exceptions\BiometricException;
use Velix\Exceptions\VelixException;

try {
    $result = $checkin->facial('slug', $frame);
} catch (AuthException $e) {
    echo 'Invalid API key';
} catch (BiometricException $e) {
    echo 'Face not recognized or liveness failed';
} catch (VelixException $e) {
    echo "HTTP {$e->getCode()}: {$e->getMessage()}";
}
```

## Running Tests

```bash
composer install
composer exec phpunit
```

## Local Development

```bash
composer install
composer exec phpunit -- --testdox
```

## Get an API Key

Access the dashboard at **velixbiometrics.com** → Settings → API Keys → New Key.
