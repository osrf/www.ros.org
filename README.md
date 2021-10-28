DEPRECATED
==========

This has been replaced by https://github.com/ros-infrastructure/www.ros.org


www.ros.org
===========

This is the www.ros.org site.

To deploy on a fresh machine with Apache (and php, and mysql, and everything else needed by Wordpress):

    cd /var
    rm -rf www
    git clone https://github.com/osrf/www.ros.org.git www
    cd www
    git checkout master
    git submodule init
    git submodule update
    
You also need to set up the database, which will involve at least something like this:

    cd /tmp
    git clone https://github.com/osrf/www.ros.org.git www.ros.org
    cd www.ros.org
    git checkout wordpressdb
    <undump the content of wordpress.sql into your mysql database>
    <configure the username and password in /var/www/wp-config.php>


Also set up the following cron jobs

    0 */6 * * * /home/ros/www.ros.org_cron_scripts/commit-db.sh > /dev/null 2> /dev/null
    0 */6 * * * /home/ros/www.ros.org_cron_scripts/commit-website.sh > /dev/null 2> /dev/null
