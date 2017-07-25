ls -1 *.json | while read jsonfile; do mongoimport --db test -c $jsonfile --file $jsonfile  --type= ; done
