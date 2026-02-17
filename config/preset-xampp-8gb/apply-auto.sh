#!/usr/bin/env bash
set -euo pipefail

PRESET_DIR="$(cd "$(dirname "$0")" && pwd)"

HTTPD_CONF="/opt/lampp/etc/httpd.conf"
PHP_INI="/opt/lampp/etc/php.ini"
MY_CNF="/opt/lampp/etc/my.cnf"
APACHE_EXTRA_CONF="/opt/lampp/etc/extra/cbt-smkalq-8gb.conf"
BACKUP_ROOT="/opt/lampp/etc/cbt-smkalq-backups"

TS="$(date +%Y%m%d-%H%M%S)"
BACKUP_DIR="$BACKUP_ROOT/$TS"

mkdir -p "$BACKUP_DIR"
cp -a "$HTTPD_CONF" "$BACKUP_DIR/httpd.conf.bak"
cp -a "$PHP_INI" "$BACKUP_DIR/php.ini.bak"
cp -a "$MY_CNF" "$BACKUP_DIR/my.cnf.bak"

write_managed_block() {
  local target_file="$1"
  local marker_name="$2"
  local content_file="$3"
  local start="# >>> CBT-SMKALQ ${marker_name} START >>>"
  local end="# <<< CBT-SMKALQ ${marker_name} END <<<"
  local tmp_file
  tmp_file="$(mktemp)"

  awk -v s="$start" -v e="$end" '
    $0==s {skip=1; next}
    $0==e {skip=0; next}
    !skip {print}
  ' "$target_file" > "$tmp_file"

  {
    cat "$tmp_file"
    echo
    echo "$start"
    cat "$content_file"
    echo "$end"
  } > "$target_file"

  rm -f "$tmp_file"
}

cp "$PRESET_DIR/httpd-8gb.conf" "$APACHE_EXTRA_CONF"

if ! grep -q 'IncludeOptional "etc/extra/cbt-smkalq-8gb.conf"' "$HTTPD_CONF"; then
  {
    echo
    echo '# CBT-SMKALQ performance preset include'
    echo 'IncludeOptional "etc/extra/cbt-smkalq-8gb.conf"'
  } >> "$HTTPD_CONF"
fi

write_managed_block "$PHP_INI" "PHP-8GB" "$PRESET_DIR/php-8gb.ini"
write_managed_block "$MY_CNF" "MYSQL-8GB" "$PRESET_DIR/mysql-8gb.cnf"

ln -sfn "$BACKUP_DIR" "$BACKUP_ROOT/latest"

/opt/lampp/bin/apachectl -t
/opt/lampp/lampp restart

echo "Applied preset successfully."
echo "Backup saved at: $BACKUP_DIR"
