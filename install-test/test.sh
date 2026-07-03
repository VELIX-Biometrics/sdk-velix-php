#!/usr/bin/env bash
# Instala via Composer "path repository", que resolve/autoload exatamente
# como instalaria do Packagist — não usa o autoload local do smoke_test.php.
set -e
cd "$(dirname "$0")/.."
REPO_DIR="$(pwd)"

rm -rf /tmp/velix-install-test-php
mkdir -p /tmp/velix-install-test-php
cd /tmp/velix-install-test-php

cat > composer.json <<EOF
{
  "name": "velix/install-test",
  "repositories": [{ "type": "path", "url": "$REPO_DIR" }],
  "require": { "velix/sdk": "*" },
  "minimum-stability": "dev",
  "prefer-stable": true
}
EOF

composer install -q --no-interaction

php -r "
require 'vendor/autoload.php';
\$client = new Velix\VelixClient(['apiUrl' => 'http://localhost', 'apiKey' => 'test']);
\$onboarding = new Velix\Modules\OnboardingModule(\$client);
if (!method_exists(\$onboarding, 'enroll')) { throw new Exception('OnboardingModule::enroll não existe no pacote instalado'); }
echo \"INSTALL_TEST:php:PASS: instalado via composer path repository, OnboardingModule::enroll existe\n\";
"
