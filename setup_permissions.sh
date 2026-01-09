#!/bin/bash

# Base directory
BASE_DIR="/opt/lampp/htdocs/cbtsmkalq"
BACKUP_DIR="/opt/lampp/backup"

# Create upload directories if not exist
echo "Creating upload directories..."
mkdir -p $BASE_DIR/pictures
mkdir -p $BASE_DIR/video
mkdir -p $BASE_DIR/audio
mkdir -p $BASE_DIR/fotosiswa
mkdir -p $BASE_DIR/images
mkdir -p $BASE_DIR/output
mkdir -p $BASE_DIR/file-excel
mkdir -p $BACKUP_DIR

# Set ownership to Apache user (daemon:daemon for XAMPP)
echo "Setting ownership to daemon:daemon..."
sudo chown -R daemon:daemon $BASE_DIR/pictures
sudo chown -R daemon:daemon $BASE_DIR/video
sudo chown -R daemon:daemon $BASE_DIR/audio
sudo chown -R daemon:daemon $BASE_DIR/fotosiswa
sudo chown -R daemon:daemon $BASE_DIR/images
sudo chown -R daemon:daemon $BASE_DIR/output
sudo chown -R daemon:daemon $BASE_DIR/file-excel
sudo chown -R daemon:daemon $BACKUP_DIR

# Set directory permissions (755 = rwxr-xr-x)
echo "Setting directory permissions..."
sudo chmod -R 755 $BASE_DIR/pictures
sudo chmod -R 755 $BASE_DIR/video
sudo chmod -R 755 $BASE_DIR/audio
sudo chmod -R 755 $BASE_DIR/fotosiswa
sudo chmod -R 755 $BASE_DIR/images
sudo chmod -R 755 $BASE_DIR/output
sudo chmod -R 755 $BASE_DIR/file-excel
sudo chmod -R 755 $BACKUP_DIR

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
