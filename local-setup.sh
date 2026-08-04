#!/bin/sh
set -eu

project_dir=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)
cd "$project_dir"

if ! command -v docker >/dev/null 2>&1; then
	printf '%s\n' 'Docker is required.' >&2
	exit 1
fi

if [ ! -f .env ]; then
	umask 077
	db_password=$(openssl rand -hex 24)
	root_password=$(openssl rand -hex 24)
	admin_password=$(openssl rand -base64 24 | tr -d '\n/=+')

	{
		printf '%s\n' 'COMPOSE_PROJECT_NAME=wordpress_site3'
		printf '%s\n' 'WORDPRESS_PORT=8082'
		printf '%s\n' 'MAILPIT_PORT=8026'
		printf '%s\n' 'WORDPRESS_DB_NAME=wordpress_site3'
		printf '%s\n' 'WORDPRESS_DB_USER=wp_site3'
		printf 'WORDPRESS_DB_PASSWORD=%s\n' "$db_password"
		printf 'MARIADB_ROOT_PASSWORD=%s\n' "$root_password"
		printf '%s\n' 'WORDPRESS_ADMIN_USER=siteadmin'
		printf 'WORDPRESS_ADMIN_PASSWORD=%s\n' "$admin_password"
		printf '%s\n' 'WORDPRESS_ADMIN_EMAIL=admin@example.test'
		printf '%s\n' "WORDPRESS_TITLE='Авторские туры'"
	} > .env

	{
		printf '%s\n' 'Local WordPress admin'
		printf '%s\n' 'URL: http://localhost:8082/wp-admin/'
		printf '%s\n' 'Username: siteadmin'
		printf 'Password: %s\n' "$admin_password"
	} > .local-admin.txt
fi

set -a
. ./.env
set +a

docker compose up -d db wordpress

attempt=0
until docker compose exec -T db healthcheck.sh --connect --innodb_initialized >/dev/null 2>&1; do
	attempt=$((attempt + 1))
	if [ "$attempt" -ge 45 ]; then
		printf '%s\n' 'Database did not become ready in time.' >&2
		exit 1
	fi
	sleep 2
done

attempt=0
until [ -f wp-config.php ]; do
	attempt=$((attempt + 1))
	if [ "$attempt" -ge 30 ]; then
		printf '%s\n' 'WordPress configuration was not generated in time.' >&2
		exit 1
	fi
	sleep 1
done

if ! docker compose --profile tools run --rm wpcli wp core is-installed --allow-root >/dev/null 2>&1; then
	docker compose --profile tools run --rm wpcli wp core install \
		--url="http://localhost:${WORDPRESS_PORT}" \
		--title="$WORDPRESS_TITLE" \
		--admin_user="$WORDPRESS_ADMIN_USER" \
		--admin_password="$WORDPRESS_ADMIN_PASSWORD" \
		--admin_email="$WORDPRESS_ADMIN_EMAIL" \
		--skip-email \
		--allow-root
fi

docker compose --profile tools run --rm wpcli wp theme activate turkey-signature --allow-root
docker compose --profile tools run --rm wpcli wp plugin activate turkey-signature-contact --allow-root
docker compose --profile tools run --rm wpcli wp eval-file wp-content/themes/turkey-signature/migrations/migrate-homepage-to-page.php --allow-root
docker compose --profile tools run --rm wpcli wp eval-file wp-content/themes/turkey-signature/migrations/migrate-homepage-conversion-tour-pages-v27.php --allow-root
docker compose --profile tools run --rm wpcli wp eval-file wp-content/themes/turkey-signature/migrations/migrate-homepage-primary-tour-v28.php --allow-root
docker compose --profile tools run --rm wpcli wp eval-file wp-content/themes/turkey-signature/migrations/migrate-contact-trigger-validation-v29.php --allow-root
docker compose --profile tools run --rm wpcli wp eval-file wp-content/themes/turkey-signature/migrations/migrate-homepage-primary-tour-content-v30.php --allow-root
docker compose --profile tools run --rm wpcli wp eval-file wp-content/themes/turkey-signature/migrations/migrate-homepage-design-refinement-v31.php --allow-root
docker compose --profile tools run --rm wpcli wp eval-file wp-content/themes/turkey-signature/migrations/migrate-homepage-visual-polish-v32.php --allow-root
docker compose --profile tools run --rm wpcli wp eval-file wp-content/themes/turkey-signature/migrations/migrate-homepage-compact-layout-v33.php --allow-root
docker compose --profile tools run --rm wpcli wp eval-file wp-content/themes/turkey-signature/migrations/migrate-homepage-program-days-v34.php --allow-root
docker compose --profile tools run --rm wpcli wp language core install ru_RU --activate --allow-root
docker compose --profile tools run --rm wpcli wp option update blogname "$WORDPRESS_TITLE" --allow-root
docker compose --profile tools run --rm wpcli wp option update blogdescription 'Авторские путешествия по Турции' --allow-root
docker compose --profile tools run --rm wpcli wp option update timezone_string 'Europe/Moscow' --allow-root
docker compose --profile tools run --rm wpcli wp rewrite structure '/%postname%/' --hard --allow-root
docker compose --profile tools run --rm wpcli wp rewrite flush --hard --allow-root

printf '%s\n' 'WordPress is ready.'
printf 'Site: http://localhost:%s/\n' "$WORDPRESS_PORT"
printf 'Admin: http://localhost:%s/wp-admin/\n' "$WORDPRESS_PORT"
printf 'Test mail: http://localhost:%s/\n' "${MAILPIT_PORT:-8026}"
printf '%s\n' 'Credentials are stored in .local-admin.txt'
