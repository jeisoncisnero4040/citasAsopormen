#!/bin/bash
set -e

REPO_URL="https://github.com/jeisoncisnero4040/citasAsopormen.git"
BRANCH="clinico"
WORKDIR="/var/www"

echo "⚔️ Git hook listener iniciado. Vigilando la rama '$BRANCH'..."

# Ciclo infinito: escucha cada 60 segundos (puede ajustarse)
while true; do
    cd $WORKDIR/clinico
    git fetch origin $BRANCH > /dev/null 2>&1
    LOCAL=$(git rev-parse $BRANCH)
    REMOTE=$(git rev-parse origin/$BRANCH)

    if [ "$LOCAL" != "$REMOTE" ]; then
        echo "🛠️ Cambios detectados en $BRANCH. Actualizando..."
        cd $WORKDIR
        rm -rf clinico
        git clone -b $BRANCH $REPO_URL clinico
        cd clinico
        composer install --no-interaction --prefer-dist --optimize-autoloader
        composer update --no-interaction
        echo "✅ Actualización completada."
    fi

    sleep 60
done