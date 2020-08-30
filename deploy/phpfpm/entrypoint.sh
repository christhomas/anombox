#!/bin/sh
set -e

echo "Configuring XDebug"
/xdebug.sh

echo "Making a temporary log just in case laravel needs it"
mkdir -p /www/storage/logs
chmod 777 /www/storage/logs

echo "Set the correct permissions"
mkdir -p /www/storage/app
chmod -Rv 777 /www/storage/app

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

echo "Executing '$@'..."
/bin/sh -c "$@"
