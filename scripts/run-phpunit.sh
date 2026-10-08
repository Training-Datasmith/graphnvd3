#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

PHPUNIT_PHAR="${PHPUNIT_PHAR:-/tmp/phpunit.phar}"
PHPUNIT_ARGS=(-c phpunit.xml.dist)

if [[ "${1:-}" == "--random" ]]; then
  shift
  PHPUNIT_ARGS+=(--order-by=random --random-order-seed=20261008)
fi

php -d error_reporting=-1 -r 'echo PHP_VERSION, "\n";'
php -d error_reporting=-1 "$PHPUNIT_PHAR" "${PHPUNIT_ARGS[@]}" "$@"
