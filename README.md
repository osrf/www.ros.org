www.ros.org
===========

This is the www.ros.org site.

To deploy on a fresh machine with Apache (and php, and mysql, and everything else needed by Wordpress):

    cd /var
    rm -rf www
    git clone https://github.com/osrf/www.ros.org.git www
    cd www
    git submodule init
    git submodule update
    
TODO: document database setup
