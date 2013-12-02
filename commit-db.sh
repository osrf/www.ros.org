#!/bin/bash

dir=`mktemp -d`
git clone -b wordpressdb git@github.com:osrf/www.ros.org.git $dir/www.ros.org
cd $dir/www.ros.org
mysqldump -u XXX -pXXX --skip-extended-insert wordpress > wordpress.sql
git add wordpress.sql
git commit -m "automatic db udpate"
git push origin wordpressdb
cd /
rm -rf $dir
