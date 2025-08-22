# Set the default goal if no targets were specified on the command line
.DEFAULT_GOAL = run
# Makes shell non-interactive and exit on any error
.SHELLFLAGS = -ec

docker-server-php:
	php artisan optimize:clear && \
	php artisan optimize && \
	php artisan filament:optimize && \
	php artisan migrate --seed --force && \
	exec frankenphp run --config /etc/caddy/Caddyfile

docker-server-queue:
	php artisan queue:work --tries=3 --timeout=90

.PHONY: \
	docker-server-php \
	docker-server-queue
