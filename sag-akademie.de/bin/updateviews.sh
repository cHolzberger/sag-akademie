#!/bin/zsh
echo "Password: "
read PASSWORD
for i in `ls ../lib/db/mysql/??-*.sql`; do
	echo "Importing $i"
	mysql -u admin --password="$PASSWORD" --host=127.0.0.1 -D sag-akademie_de_stable < $i
done
