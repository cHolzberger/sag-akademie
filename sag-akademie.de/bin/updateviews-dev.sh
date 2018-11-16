#!/bin/zsh
echo "Password: "
read PASSWORD
for i in `ls ../lib/db/mysql/??-*.sql`; do
	echo "Importing $i"
	mysql -u root --password="$PASSWORD" --host=mysqldb -D dev_sagakademie < $i
done
