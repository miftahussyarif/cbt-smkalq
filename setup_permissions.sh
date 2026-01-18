#!/bin/bash

# Base directory
BASE_DIR="/opt/lampp/htdocs/cbt-smkalq"
BACKUP_DIR="/opt/lampp/backup"
WEB_USER="daemon"
WEB_GROUP="daemon"
OWNER_USER="${SUDO_USER:-$(id -un)}"
OWNER_GROUP="$(id -gn "${SUDO_USER:-$(id -un)}")"

UPLOAD_DIRS=(
  "$BASE_DIR/pictures"
  "$BASE_DIR/video"
  "$BASE_DIR/audio"
  "$BASE_DIR/fotosiswa"
  "$BASE_DIR/images"
  "$BASE_DIR/output"
  "$BASE_DIR/file-excel"
)

if [ ! -d "$BASE_DIR" ]; then
  echo "Base directory not found: $BASE_DIR"
  exit 1
fi

# Create upload directories if not exist
echo "Creating upload directories..."
sudo mkdir -p "${UPLOAD_DIRS[@]}" "$BACKUP_DIR"

# Set ownership to project owner for code edits
echo "Setting ownership to $OWNER_USER:$OWNER_GROUP..."
sudo chown -R "$OWNER_USER":"$OWNER_GROUP" "$BASE_DIR"

# Set default permissions for project files
echo "Setting project file permissions..."
sudo find "$BASE_DIR" -type d -exec chmod 755 {} \;
sudo find "$BASE_DIR" -type f -exec chmod 644 {} \;

# Set ownership to Apache user (daemon:daemon for XAMPP)
echo "Setting ownership to $WEB_USER:$WEB_GROUP..."
sudo chown -R "$WEB_USER":"$WEB_GROUP" "${UPLOAD_DIRS[@]}" "$BACKUP_DIR"

# Set directory permissions (775 = rwxrwxr-x)
echo "Setting writable directory permissions..."
sudo chmod -R 775 "${UPLOAD_DIRS[@]}" "$BACKUP_DIR"

echo ""
echo "Done! Folder permissions have been set."
echo ""
echo "Folders configured:"
echo "  - $BASE_DIR/pictures"
echo "  - $BASE_DIR/video"
echo "  - $BASE_DIR/audio"
echo "  - $BASE_DIR/fotosiswa"
echo "  - $BASE_DIR/images"
echo "  - $BASE_DIR/output"
echo "  - $BASE_DIR/file-excel"
echo "  - $BACKUP_DIR"
