#!/bin/sh
set -e

# PUID/PGID Synchronization
# Environment variables PUID and PGID can be set to specify the desired UID/GID for www-data.
TARGET_UID="$PUID"
TARGET_GID="$PGID"

CURRENT_WWW_DATA_UID=$(id -u www-data)
CURRENT_WWW_DATA_GROUP_GID=$(getent group www-data | cut -d: -f3)

# Change GID of 'www-data' group if TARGET_GID is set and different
if [ -n "$TARGET_GID" ] && [ "$CURRENT_WWW_DATA_GROUP_GID" != "$TARGET_GID" ]; then
    echo "Changing GID of 'www-data' group from $CURRENT_WWW_DATA_GROUP_GID to $TARGET_GID"
    # Check if a group with TARGET_GID already exists and is not 'www-data'
    EXISTING_GROUP_WITH_TARGET_GID=$(getent group "$TARGET_GID" | cut -d: -f1)
    if [ -n "$EXISTING_GROUP_WITH_TARGET_GID" ] && [ "$EXISTING_GROUP_WITH_TARGET_GID" != "www-data" ]; then
        echo "Warning: GID $TARGET_GID is already in use by group '$EXISTING_GROUP_WITH_TARGET_GID'."
        echo "Attempting to change GID of 'www-data' group to $TARGET_GID (non-unique)."
        groupmod -o -g "$TARGET_GID" www-data
    else
        # GID is free, or already belongs to 'www-data' (but getent reported different - rare), or no group has this GID
        groupmod -g "$TARGET_GID" www-data
    fi
fi

# Change UID of 'www-data' user if TARGET_UID is set and different
if [ -n "$TARGET_UID" ] && [ "$CURRENT_WWW_DATA_UID" != "$TARGET_UID" ]; then
    echo "Changing UID of 'www-data' user from $CURRENT_WWW_DATA_UID to $TARGET_UID"
    usermod -o -u "$TARGET_UID" www-data
fi

mkdir -p storage/app/public storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
php artisan filament:assets
ln -sf /app/storage/app/public /app/public/storage

# Start cronn in background
cronn -c /app/docker/cron-config.yml &

# Start FrankenPHP
if [ $# -eq 0 ]; then
    # Default: start FrankenPHP server
    exec frankenphp run --config /etc/caddy/Caddyfile
else
    # Custom command provided
    exec "$@"
fi
