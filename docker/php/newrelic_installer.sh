#!/usr/bin/env bash

# new relic release
# VERSION is passed from the arguments at build time
export NR_NAME="newrelic-php5-$VERSION-linux"

# extract in current directory
curl -O https://download.newrelic.com/php_agent/archive/${VERSION}/${NR_NAME}.tar.gz

ls -al ${NR_NAME}.tar.gz
tar xvf ${NR_NAME}.tar.gz

export NR_INSTALL_USE_CP_NOT_LN=1 && export NR_INSTALL_SILENT=1 && ./${NR_NAME}/newrelic-install install

rm -rf ${NR_NAME}*

sed -i \
    -e 's/"REPLACE_WITH_REAL_KEY"/"'${NR_LICENSE_KEY}'"/' \
    -e 's/newrelic.appname = "PHP Application"/newrelic.appname = "App-Backend-V3"/' \
    /usr/local/etc/php/conf.d/newrelic.ini