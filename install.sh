#!/usr/bin/env bash
set -euo pipefail

# Master folder setup for CBT SMK AL QODIRIYAH
# Creates missing directories and applies ownership/permissions
# to match the currently used project pattern.

BASE_DIR="$(cd "$(dirname "$0")" && pwd)"
BACKUP_DIR="/opt/lampp/backup"
LOG_DIR="/tmp/cbt-smkalq-log"
SCHEMA_FILE="$BASE_DIR/database/schema.sql"

DB_HOST="${DB_HOST:-localhost}"
DB_PORT="${DB_PORT:-3306}"
DB_USER="${DB_USER:-root}"
DB_PASS="${DB_PASS:-}"
DB_NAME="${DB_NAME:-beesmartv3}"

# Keep ownership aligned with current project owner.
OWNER_USER="${OWNER_USER:-$(stat -c '%U' "$BASE_DIR")}"
OWNER_GROUP="${OWNER_GROUP:-$(stat -c '%G' "$BASE_DIR")}"

if [[ ! -d "$BASE_DIR" ]]; then
  echo "Base directory not found: $BASE_DIR"
  exit 1
fi

if [[ "$EUID" -eq 0 ]]; then
  RUN_AS_ROOT=""
elif command -v sudo >/dev/null 2>&1; then
  RUN_AS_ROOT="sudo"
else
  RUN_AS_ROOT=""
fi

run_cmd() {
  if [[ -n "$RUN_AS_ROOT" ]]; then
    $RUN_AS_ROOT "$@"
  else
    "$@"
  fi
}

mysql_cmd=(
  mysql
  -h "$DB_HOST"
  -P "$DB_PORT"
  -u "$DB_USER"
)
if [[ -n "$DB_PASS" ]]; then
  mysql_cmd+=("-p$DB_PASS")
fi

ensure_database_tables() {
  if ! command -v mysql >/dev/null 2>&1; then
    echo "mysql client tidak ditemukan. Lewati setup tabel."
    return 0
  fi

  if [[ ! -f "$SCHEMA_FILE" ]]; then
    echo "Schema file tidak ditemukan: $SCHEMA_FILE"
    return 1
  fi

  echo "==> Ensuring database $DB_NAME exists"
  "${mysql_cmd[@]}" -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\`;"

  echo "==> Ensuring tables exist from $SCHEMA_FILE"
  sed -E \
    -e '/^DROP TABLE IF EXISTS/d' \
    -e 's/^CREATE TABLE `/CREATE TABLE IF NOT EXISTS `/' \
    "$SCHEMA_FILE" | "${mysql_cmd[@]}" "$DB_NAME"
}

PROJECT_DIRS_755=(
  "config"
  "css"
  "database"
  "dist"
  "images"
  "js"
  "lib"
  "MathJax"
  "panel"
  "panel/pages"
  "file-excel"
)

PROJECT_DIRS_777=(
  "audio"
  "video"
  "pictures"
)

PROJECT_DIRS_757=(
  "fotosiswa"
  "output"
)

EXTERNAL_DIRS=(
  "$BACKUP_DIR"
  "$LOG_DIR"
)

echo "==> Creating required project directories"
for rel in "${PROJECT_DIRS_755[@]}" "${PROJECT_DIRS_777[@]}" "${PROJECT_DIRS_757[@]}"; do
  run_cmd mkdir -p "$BASE_DIR/$rel"
done

echo "==> Creating external directories"
for path in "${EXTERNAL_DIRS[@]}"; do
  run_cmd mkdir -p "$path"
done

echo "==> Setting ownership to $OWNER_USER:$OWNER_GROUP"
run_cmd chown -R "$OWNER_USER:$OWNER_GROUP" "$BASE_DIR"

# External dirs can be shared with web process; keep daemon ownership if available.
if id -u daemon >/dev/null 2>&1; then
  run_cmd chown -R daemon:daemon "$BACKUP_DIR" "$LOG_DIR"
fi

echo "==> Applying default directory/file modes in project"
run_cmd find "$BASE_DIR" -type d -exec chmod 755 {} \;
run_cmd find "$BASE_DIR" -type f -exec chmod 644 {} \;
run_cmd chmod 755 "$BASE_DIR/install.sh"

echo "==> Applying writable directory modes (matching current usage)"
for rel in "${PROJECT_DIRS_777[@]}"; do
  run_cmd chmod 777 "$BASE_DIR/$rel"
done
for rel in "${PROJECT_DIRS_757[@]}"; do
  run_cmd chmod 757 "$BASE_DIR/$rel"
done

# Keep external dirs writable for service operations.
run_cmd chmod 755 "$BACKUP_DIR" "$LOG_DIR"

echo "==> Initializing database tables"
ensure_database_tables

echo
echo "Done. Folder setup completed."
echo
echo "Summary:"
echo "  Owner project : $OWNER_USER:$OWNER_GROUP"
echo "  777 dirs      : ${PROJECT_DIRS_777[*]}"
echo "  757 dirs      : ${PROJECT_DIRS_757[*]}"
echo "  755 dirs      : ${PROJECT_DIRS_755[*]}"
echo "  External dirs : $BACKUP_DIR, $LOG_DIR"
echo "  Database      : $DB_NAME @ $DB_HOST:$DB_PORT"
echo
echo "Current permissions:"
ls -ld \
  "$BASE_DIR/audio" \
  "$BASE_DIR/video" \
  "$BASE_DIR/pictures" \
  "$BASE_DIR/fotosiswa" \
  "$BASE_DIR/output" \
  "$BASE_DIR/file-excel" \
  "$BASE_DIR/images" \
  "$BACKUP_DIR" \
  "$LOG_DIR"
