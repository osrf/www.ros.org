#!/bin/bash

cd /var/www/www.ros.org
git add wp-content
git commit -m "automatic wp-content update"
git push origin master
