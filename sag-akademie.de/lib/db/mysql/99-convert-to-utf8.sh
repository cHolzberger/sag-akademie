PASSWORD=xxx
mysql -u root -p$PASSWORD --database=dev_sagakademie -B -N -e "SHOW FULL TABLES" | \
grep -v VIEW  | \
awk '{print "ALTER TABLE", $1, "CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;"}' | \
mysql -u root -p$PASSWORD  --database=dev_sagakademie