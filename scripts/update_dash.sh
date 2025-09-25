#!/bin/bash

BACKUP_DIR="/var/www"
OLD_HTML_DIR="$BACKUP_DIR/html"
DATE=$(date +"%d%m%Y-%H%M%S")
BACKUP_NAME="html.$DATE.tar.gz"
REPO_URL="https://github.com/FRS077/RoLinkX-Dashboard.git"
CLONE_DIR="$BACKUP_DIR/RoLinkX-Dashboard"

# Sauvegarde de l'ancien répertoire html
if [ -d "$OLD_HTML_DIR" ]; then
  echo "Sauvegarde de $OLD_HTML_DIR en $BACKUP_DIR/$BACKUP_NAME"
  tar -czf "$BACKUP_DIR/$BACKUP_NAME" -C "$BACKUP_DIR" html
  echo "Suppression de $OLD_HTML_DIR"
  rm -rf "$OLD_HTML_DIR"
fi

# Suppression éventuelle d'un ancien clone
if [ -d "$CLONE_DIR" ]; then
  echo "Suppression de l'ancien clonage $CLONE_DIR"
  rm -rf "$CLONE_DIR"
fi

# Clonage du dépôt dans /var/www/
echo "Clonage du dépôt Git dans $CLONE_DIR"
git clone "$REPO_URL" "$CLONE_DIR"

# Renommage du dossier cloné en html
echo "Renommage de $CLONE_DIR en $OLD_HTML_DIR"
mv "$CLONE_DIR" "$OLD_HTML_DIR"

echo "Mise à jour terminée."

