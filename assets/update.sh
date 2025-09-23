bash
#!/bin/bash

# Chemin local du dashboard
LOCAL_DIR="/var/www/html"

# Chemin du dossier contenant les mises à jour (exemple : montage réseau ou dossier local)
UPDATE_DIR="/chemin/vers/updates"

# Fichier temporaire pour stocker la liste des fichiers modifiés
TMP_DIFF="/tmp/updates_diff.txt"

# Vérifier que le dossier de mise à jour existe
if [ ! -d "$UPDATE_DIR" ]; then
  echo "Le dossier des mises à jour $UPDATE_DIR n'existe pas."
  exit 1
fi

# Vérifier les fichiers différents entre le dossier local et dossier mise à jour
# Enregistre la liste des fichiers différents dans TMP_DIFF
rsync -rcn --delete "$UPDATE_DIR/" "$LOCAL_DIR/" > "$TMP_DIFF"

# Si aucun fichier différent, sortie
if [ ! -s "$TMP_DIFF" ]; then
  echo "Aucune modification détectée, aucune mise à jour effectuée."
  exit 0
fi

echo "Modifications détectées :"
cat "$TMP_DIFF"
echo "Mise à jour en cours..."

# Copier uniquement les fichiers modifiés
rsync -rc --delete "$UPDATE_DIR/" "$LOCAL_DIR/"

echo "Mise à jour terminée."
exit 0
