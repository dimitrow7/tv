#!/bin/bash

PHP_INI_SCAN_DIR=/home/resinwoo/.sh.phpmanager/php83.d
export PHP_INI_SCAN_DIR

DEFAULTPHPINI=/home/resinwoo/bitsee.eu/barhey2/php83-fcgi.ini
exec /opt/cpanel/ea-php83/root/usr/bin/php-cgi -c ${DEFAULTPHPINI}
