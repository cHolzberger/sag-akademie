#!/bin/zsh

ls ../lib/db/mysql/??-*.sql | while read i; do
echo "importing $i"
mysql -u root --password="J85sc83H" --host=srv-mi-01 -D dev_sagakademie < $i
done
