#!/bin/bash

cd /var/www
git add wp-content
git commit -m "automatic wp-content update"
git push origin master
