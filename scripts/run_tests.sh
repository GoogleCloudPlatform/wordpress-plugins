#!/bin/bash
# Copyright 2016 Google Inc.
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.  This program is distributed in
# the hope that it will be useful, but WITHOUT ANY WARRANTY; without
# even the implied warranty of MERCHANTABILITY or FITNESS FOR A
# PARTICULAR PURPOSE.  See the GNU General Public License for more
# details.  You should have received a copy of the GNU General Public
# License along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
# 02110-1301, USA.

set -ex

# run php-cs-fixer
if [ "${RUN_CS_FIXER}" = "true" ]; then
    ${HOME}/php-cs-fixer fix --dry-run --diff
fi

# download phpunit 5.7
if [ "${INSTALL_PHPUNIT}" = "true" ]; then
    mkdir -p ${HOME}/bin
    wget https://phar.phpunit.de/phpunit-5.7.phar
    mv phpunit-5.7.phar ${HOME}/bin/phpunit
    chmod +x ${HOME}/bin/phpunit
fi

# loop through all directories containing "phpunit.xml*" and run them
find * -name 'phpunit.xml*' -not -path '*/vendor/*' -exec dirname {} \; | while read DIR
do
    pushd ${DIR}
    if [ -f "composer.json" ]; then
        composer install
    fi
    echo "running phpunit in ${DIR}"
    bin/install-wp-tests.sh wordpress_test root '' ${DB_HOST} ${WP_VERSION}
    phpunit
    if [ -f build/logs/clover.xml ]; then
        cp build/logs/clover.xml \
            ${TEST_BUILD_DIR}/build/logs/clover-${DIR//\//_}.xml
    fi
    popd
done
