#!/bin/bash

set -e
dir=`mktemp -d`
git clone -b wordpressdb git@github.com:osrf/www.ros.org.git $dir/www.ros.org
cd $dir/www.ros.org
mysqldump -h XXX -u XXX -pXXX --skip-extended-insert XXX > wordpress.sql
git add wordpress.sql
git commit -m "automatic db udpate"
git push origin wordpressdb
cd /
rm -rf $dir
