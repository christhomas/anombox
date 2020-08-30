#!/bin/sh

DEFAULT_PHPFPM_HOST=anombox-phpfpm
[ -z "${PHPFPM_HOST}" ] && echo "PHPFPM_HOST missing, setting to default '${DEFAULT_PHPFPM_HOST}'" && export -p PHPFPM_HOST=${DEFAULT_PHPFPM_HOST}
[ -z "${PHPFPM_PORT}" ] && echo "PHPFPM_PORT missing, setting to default '9000'" && export -p PHPFPM_PORT=9000

echo "PHPFPM_HOST was configured to use '${PHPFPM_HOST}'"
echo "PHPFPM_PORT was configured to use '${PHPFPM_PORT}'"

envsubst '\${PHPFPM_HOST} \${PHPFPM_PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

echo "Executing application entrypoint '$@'"
exec "$@"
