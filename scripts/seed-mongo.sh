#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

if [[ ! -f "$ROOT_DIR/.env" ]]; then
  echo "Missing .env file. Copy .env.example to .env first." >&2
  exit 1
fi

set -a
source "$ROOT_DIR/.env"
set +a

MONGO_URI="mongodb://${MONGO_INITDB_ROOT_USERNAME}:${MONGO_INITDB_ROOT_PASSWORD}@127.0.0.1:27017/admin?authSource=admin"

COUNT="${COUNT:-240}"
DAYS="${DAYS:-180}"

echo "Seeding MongoDB (${MONGO_DB:-vite_gourmand}) with COUNT=$COUNT, DAYS=$DAYS..."

docker run --rm -it --network=host \
  -e MONGO_DB="${MONGO_DB:-vite_gourmand}" \
  -e COUNT="$COUNT" \
  -e DAYS="$DAYS" \
  -v "$ROOT_DIR/db/seed_mongo.js:/seed_mongo.js:ro" \
  mongo:7 mongosh "$MONGO_URI" /seed_mongo.js
