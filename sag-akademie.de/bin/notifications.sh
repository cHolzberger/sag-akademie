#!/bin/sh 
PROJECTDIR=$( dirname "$0" ) 
cd $PROJECTDIR/../

export HTTP_HOST="`hostname -f`"
php _notifications.php
