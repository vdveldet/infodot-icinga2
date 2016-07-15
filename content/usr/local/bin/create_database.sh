#!/bin/bash

if [ -d /var/lib/mysql/icinga ]; then
	echo "Icinga database has been found, reusing"
else
	echo "CREATING Icinga database"
        mysql_install_db --user=mysql --ldata=/var/lib/mysql 2>&1 >/dev/null
        /usr/bin/mysqld_safe 2>&1 >/dev/null &
        sleep 10s
        mysql -uroot -e "CREATE DATABASE IF NOT EXISTS icinga ; GRANT ALL ON icinga.* TO icinga@localhost IDENTIFIED BY 'icinga';"
        mysql -uicinga -picinga icinga < /usr/share/icinga2-ido-mysql/schema/mysql.sql
        mysql -uroot -e "CREATE DATABASE IF NOT EXISTS icingaweb2 ; GRANT ALL ON icingaweb2.* TO icingaweb2@localhost IDENTIFIED BY 'icingaweb2';"
        mysql -uicingaweb2 -picingaweb2 icingaweb2 < /usr/share/doc/icingaweb2/schema/mysql.schema.sql
        mysql -uicingaweb2 -picingaweb2 icingaweb2 -e "INSERT INTO icingaweb_user (name, active, password_hash) VALUES ('icingaadmin', 1, '\$1\$iQSrnmO9\$T3NVTu0zBkfuim4lWNRmH.');"
        killall mysqld
fi
