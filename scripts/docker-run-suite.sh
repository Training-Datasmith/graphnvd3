#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
IMAGE="${1:?image digest or tag required}"
PHAR="${2:?phar path required}"
MODE="${3:-default}"

ARGS=(-c phpunit.xml.dist)
if [[ "$MODE" == "random" ]]; then
  ARGS+=(--order-by=random --random-order-seed=20261008)
fi

docker run --rm \
  -v "$ROOT:/app" \
  -v "$PHAR:/tmp/phpunit.phar:ro" \
  -w /app \
  "$IMAGE" \
  sh -c 'php -d error_reporting=-1 -r "echo PHP_VERSION, PHP_EOL;" && php -d error_reporting=-1 /tmp/phpunit.phar '"${ARGS[*]}"
