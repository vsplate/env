#!/bin/bash

mysql -u root -ptoor < /tmp/db.sql
chmod -R 777 /var/www/html
