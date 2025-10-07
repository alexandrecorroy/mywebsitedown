#!/bin/sh
set -e

# S'assure que www-data possède bien les bons droits
echo "Vérification des droits sur /var/www/symfony..."
chown -R www-data:www-data /var/www/symfony/var /var/www/symfony/vendor 2>/dev/null || true

if [ "$APP_ENV" = "dev" ]; then
  if [ ! -d "vendor" ] || [ ! -f "vendor/autoload_runtime.php" ]; then
    echo "Installation des dépendances Composer..."
    composer install --no-interaction || true
    composer require --dev doctrine/doctrine-fixtures-bundle --no-interaction || true
  fi

  echo "Attente de la base de données..."
  MAX_RETRIES=30
  RETRIES=0

  DB_USER="${MARIADB_USER}"
  DB_PASS="${MARIADB_PASSWORD}"
  DB_HOST="${MARIADB_HOST}"
  DB_PORT="${MARIADB_PORT:-3306}"
  DB_NAME="${MARIADB_DATABASE}"

  until php -r "new PDO('mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME', '$DB_USER', '$DB_PASS');" 2>/dev/null; do
    RETRIES=$((RETRIES+1))
    if [ "$RETRIES" -ge "$MAX_RETRIES" ]; then
      echo "La base de données n'est pas accessible après ${MAX_RETRIES}s."
      exit 1
    fi
    sleep 1
  done

  echo "Base de données disponible."
  echo "Migration de la base..."
  php bin/console doctrine:migrations:migrate --no-interaction || true

  echo "Chargement des fixtures..."
  php bin/console doctrine:fixtures:load --no-interaction || true
fi

exec "$@"
