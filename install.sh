#!/usr/bin/env bash
set -euo pipefail

# Master folder setup for CBT SMK AL QODIRIYAH
# Creates missing directories and applies ownership/permissions
# to match the currently used project pattern.

BASE_DIR="$(cd "$(dirname "$0")" && pwd)"
BACKUP_DIR="$BASE_DIR/backup"
LOG_DIR="$BASE_DIR/logs"
SCHEMA_FILE="$BASE_DIR/database/schema.sql"
LEGACY_BACKUP_DIR="/opt/lampp/backup"
LEGACY_LOG_DIR="/tmp/cbt-smkalq-log"

DB_HOST="${DB_HOST:-localhost}"
DB_PORT="${DB_PORT:-3306}"
DB_USER="${DB_USER:-root}"
DB_PASS="${DB_PASS:-}"
DB_NAME="${DB_NAME:-beesmartv3}"

detect_user() {
  for candidate in "$@"; do
    if id -u "$candidate" >/dev/null 2>&1; then
      echo "$candidate"
      return 0
    fi
  done
  return 1
}

detect_group() {
  for candidate in "$@"; do
    if getent group "$candidate" >/dev/null 2>&1; then
      echo "$candidate"
      return 0
    fi
  done
  return 1
}

if [[ ! -d "$BASE_DIR" ]]; then
  echo "Base directory not found: $BASE_DIR"
  exit 1
fi

PROJECT_OWNER_USER="$(stat -c '%U' "$BASE_DIR")"
PROJECT_OWNER_GROUP="$(stat -c '%G' "$BASE_DIR")"
PREFERRED_WEB_USER="$(detect_user www-data daemon || true)"

# Default owner is web user for new server deploy (www-data/daemon),
# can be overridden via OWNER_USER/OWNER_GROUP env variables.
if [[ -z "${OWNER_USER:-}" ]]; then
  if [[ -n "$PREFERRED_WEB_USER" ]]; then
    OWNER_USER="$PREFERRED_WEB_USER"
  else
    OWNER_USER="$PROJECT_OWNER_USER"
  fi
fi

if [[ -z "${OWNER_GROUP:-}" ]]; then
  if id -gn "$OWNER_USER" >/dev/null 2>&1; then
    OWNER_GROUP="$(id -gn "$OWNER_USER")"
  else
    OWNER_GROUP="$(detect_group www-data daemon || true)"
    OWNER_GROUP="${OWNER_GROUP:-$PROJECT_OWNER_GROUP}"
  fi
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

migrate_legacy_dir() {
  local from_dir="$1"
  local to_dir="$2"
  local label="$3"

  if [[ "$from_dir" == "$to_dir" ]]; then
    return 0
  fi

  if [[ -d "$from_dir" ]]; then
    echo "==> Migrating legacy $label from $from_dir to $to_dir"
    run_cmd mkdir -p "$to_dir"
    run_cmd find "$from_dir" -mindepth 1 -maxdepth 1 -exec mv -t "$to_dir" {} +
    run_cmd rmdir "$from_dir" 2>/dev/null || true
  fi
}

PROJECT_DIRS_755=(
  "config"
  "css"
  "database"
  "dist"
  "js"
  "lib"
  "MathJax"
  "panel"
  "panel/pages"
  "file-excel"
)

PROJECT_DIRS_WRITABLE=(
  "audio"
  "video"
  "pictures"
  "images"
  "pictures_webp"
  "fotosiswa"
  "output"
  "backup"
  "logs"
)

echo "==> Creating required project directories"
for rel in "${PROJECT_DIRS_755[@]}" "${PROJECT_DIRS_WRITABLE[@]}"; do
  run_cmd mkdir -p "$BASE_DIR/$rel"
done

echo "==> Setting ownership to $OWNER_USER:$OWNER_GROUP"
if [[ -d "$BASE_DIR/.git" ]]; then
  # Keep .git owned by repository user to avoid git permission issues.
  run_cmd find "$BASE_DIR" -mindepth 1 -maxdepth 1 ! -name ".git" -exec chown -R "$OWNER_USER:$OWNER_GROUP" {} +
else
  run_cmd chown -R "$OWNER_USER:$OWNER_GROUP" "$BASE_DIR"
fi

echo "==> Migrating legacy external directories (if any)"
migrate_legacy_dir "$LEGACY_BACKUP_DIR" "$BACKUP_DIR" "backup files"
migrate_legacy_dir "$LEGACY_LOG_DIR" "$LOG_DIR" "log files"

echo "==> Applying default directory/file modes in project"
if [[ -d "$BASE_DIR/.git" ]]; then
  run_cmd find "$BASE_DIR" -path "$BASE_DIR/.git" -prune -o -type d -exec chmod 755 {} \;
  run_cmd find "$BASE_DIR" -path "$BASE_DIR/.git" -prune -o -type f -exec chmod 644 {} \;
  run_cmd find "$BASE_DIR" -path "$BASE_DIR/.git" -prune -o -type f -name '*.sh' -exec chmod 755 {} \;
else
  run_cmd find "$BASE_DIR" -type d -exec chmod 755 {} \;
  run_cmd find "$BASE_DIR" -type f -exec chmod 644 {} \;
  run_cmd find "$BASE_DIR" -type f -name '*.sh' -exec chmod 755 {} \;
fi

echo "==> Applying writable directory modes (web-user friendly)"
for rel in "${PROJECT_DIRS_WRITABLE[@]}"; do
  run_cmd chmod 775 "$BASE_DIR/$rel"
done

# Keep internal storage dirs writable for service operations.
run_cmd chmod 775 "$BACKUP_DIR" "$LOG_DIR"

# Ensure backup/media storage can be overwritten during restore operations.
echo "==> Applying recursive writable modes for backup/media storage"
STORAGE_WRITE_DIRS=(
  "$BASE_DIR/pictures"
  "$BASE_DIR/audio"
  "$BASE_DIR/video"
  "$BASE_DIR/output"
  "$BASE_DIR/logs"
)
run_cmd chown -R "$OWNER_USER:$OWNER_GROUP" "${STORAGE_WRITE_DIRS[@]}"
for storage_dir in "${STORAGE_WRITE_DIRS[@]}"; do
  run_cmd find "$storage_dir" -type d -exec chmod 777 {} \;
  run_cmd find "$storage_dir" -type f -exec chmod 666 {} \;
done

# Student-photo upload folder must stay fully writable on fresh servers.
echo "==> Applying recursive writable modes for fotosiswa storage"
run_cmd chown -R "$OWNER_USER:$OWNER_GROUP" "$BASE_DIR/fotosiswa"
run_cmd find "$BASE_DIR/fotosiswa" -type d -exec chmod 777 {} \;
run_cmd find "$BASE_DIR/fotosiswa" -type f -exec chmod 666 {} \;

# Backup directory must stay fully writable for backup/restore flow in legacy PHP modules.
echo "==> Applying recursive writable modes for backup storage"
run_cmd chown -R "$OWNER_USER:$OWNER_GROUP" "$BACKUP_DIR"
run_cmd find "$BACKUP_DIR" -type d -exec chmod 777 {} \;
run_cmd find "$BACKUP_DIR" -type f -exec chmod 666 {} \;

echo "==> Initializing database tables"
ensure_database_tables

echo
echo "Done. Folder setup completed."
echo
echo "Summary:"
echo "  Owner project : $OWNER_USER:$OWNER_GROUP"
echo "  Writable dirs : ${PROJECT_DIRS_WRITABLE[*]}"
echo "  755 dirs      : ${PROJECT_DIRS_755[*]}"
echo "  Storage dirs  : $BACKUP_DIR, $LOG_DIR"
echo "  Database      : $DB_NAME @ $DB_HOST:$DB_PORT"
echo
echo "Current permissions:"
ls -ld \
  "$BASE_DIR/audio" \
  "$BASE_DIR/video" \
  "$BASE_DIR/pictures" \
  "$BASE_DIR/pictures_webp" \
  "$BASE_DIR/fotosiswa" \
  "$BASE_DIR/output" \
  "$BASE_DIR/file-excel" \
  "$BASE_DIR/images" \
  "$BASE_DIR/backup" \
  "$BASE_DIR/logs"
