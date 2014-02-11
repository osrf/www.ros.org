#!/bin/bash

set -e
dir=`mktemp -d`
git clone -b wordpressdb git@github.com:osrf/www.ros.org.git $dir/www.ros.org
cd $dir/www.ros.org
mysqldump -h XXX -u XXX -pXXX --skip-extended-insert XXX | sed -e 's/^-- Dump completed on .*//' > wordpress.sql
git add wordpress.sql
# if there are no changes commit will return with != 0
git commit -m "automatic db update" || true
git push origin wordpressdb
cd /
rm -rf $dir
