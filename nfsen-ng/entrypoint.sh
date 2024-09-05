#!/bin/bash
sources=`cat /tmp/sources.conf | sed -re "s/^([^;]*);.*$/'\1',/" | tr '\n' ' ' `
sed -e "s/'router',/$sources/" /var/www/html/backend/settings/settings.tmpl > /var/www/html/backend/settings/settings.php

if php -f backend/settings/settings.php; then
    /var/www/html/backend/cli.php start
#    apachectl -D FOREGROUND
    apache2-foreground
fi
