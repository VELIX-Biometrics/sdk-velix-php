# velixbiometrics/sdk — PHP SDK ![version](https://img.shields.io/badge/version-0.1.0--alpha1-blue)

> ⚠️ **Alpha / pre-release**, mas já publicado e confirmado funcionando de ponta a ponta contra a API real de staging (onboarding, LGPD, me, events). **Packagist:** https://packagist.org/packages/velixbiometrics/sdk

Official PHP SDK for the VELIX Biometrics platform — facial access control B2B SaaS.

## Requirements

- PHP 8.1+
- ext-json
- Composer

## Installation

```bash
composer require velixbiometrics/sdk
```

## Auth

All requests are authenticated with an API key issued per tenant application. Send it via
the `x-api-key` header (default) or `Authorization: Bearer vlx_...` (accepted by the same
guard). Never use any other auth mechanism.

## Quick Start

```php
use Velix\VelixClient;
use Velix\Modules\CheckinModule;

$client = new VelixClient([
    'apiUrl'  => $_ENV['VELIX_API_URL'],
    'apiKey'  => $_ENV['VELIX_API_KEY'],
    'timeout' => 30, // seconds; default is 30s (30000ms) per SDK contract, always overridable
]);

$result = (new CheckinModule($client))->identify($frameBase64);
echo $result->matched ? 'MATCHED' : 'NOT MATCHED';
```

## Environment Variables

| Variable | Required | Description |
|----------|----------|-------------|
| `VELIX_API_URL` | Yes | API base URL (`https://api.velixbiometrics.com`) |
| `VELIX_API_KEY` | Yes | API key (`vlx_...`) |

## Modules

Only the 6 real endpoints of `/v1/api/*` are implemented. `PersonsModule`,
`TenantsModule` and `WebhooksModule::configure()` are kept for backward-compatible
autoloading only — every method throws `RuntimeException` since no corresponding
endpoint exists on the real API.

| Module | Method | Endpoint | Scope |
|--------|--------|----------|-------|
| `OnboardingModule` | `enroll()` | `POST /v1/api/onboarding` | `onboarding:write` |
| `CheckinModule` | `identify()` | `POST /v1/api/checkin/identify` | `checkin:write` |
| `LgpdModule` | `requestDeletion()` | `POST /v1/api/deletion-request` | `lgpd:write` |
| `MeModule` | `get()` | `GET /v1/api/me/{personId}` | `me:read` |
| `EventsModule` | `createGuest()` | `POST /v1/api/events/{id}/guests` | `events:write` |
| `EventsModule` | `getGuest()` | `GET /v1/api/events/{id}/guests/{guestId}` | `events:read` |

**Velix Time is not covered.** No `/v1/api/time/*` endpoint exists in the backend yet
(see task #616) — there is no Time-related class in this SDK to call.

## Onboarding Module

```php
use Velix\Modules\OnboardingModule;

$onboarding = new OnboardingModule($client);

$result = $onboarding->enroll('João Silva', [$frame1, $frame2, $frame3], [
    'email' => 'joao@company.com',
    'external_id' => 'EMP-001',
]);

// $result->personId, $result->identityId, $result->enrolled, $result->framesProcessed
```

## Checkin Module

```php
use Velix\Modules\CheckinModule;

$checkin = new CheckinModule($client);

$result = $checkin->identify($frameBase64, [
    'topK' => 3,
    'location' => ['latitude' => -23.55, 'longitude' => -46.63],
]);

// $result->matched, $result->personId, $result->qualityScore, $result->message
// Liveness score is never exposed — only booleans/messages.
```

`qr()` and `pin()` throw `RuntimeException` — no such endpoint exists in `/v1/api/*`.

## Me Module

```php
use Velix\Modules\MeModule;

$me = (new MeModule($client))->get('person-uuid');
// $me->id, $me->name, $me->email, $me->phone, $me->photoUrl, $me->createdAt
```

## LGPD Module

```php
use Velix\Modules\LgpdModule;

$result = (new LgpdModule($client))->requestDeletion('person-uuid');
// $result->protocolNumber, $result->message
```

## Events Module

```php
use Velix\Modules\EventsModule;

$events = new EventsModule($client);

$guest = $events->createGuest('event-uuid', [
    'name' => 'Maria Souza',
    'email' => 'maria@example.com',
]);

$guest = $events->getGuest('event-uuid', $guest->id);
```

`list()`, `get()`, `create()`, `configure()`, `delete()` throw `RuntimeException` — no such
event-management endpoints exist in `/v1/api/*`.

## Webhook Signature Validation

`WebhooksModule::validateSignature()` is a pure local helper (no HTTP call) and remains
functional. `WebhooksModule::configure()` throws `RuntimeException` — no matching endpoint.

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
    $result = $checkin->identify($frameBase64);
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
