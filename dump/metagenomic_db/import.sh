ls -1 *.json | while read jsonfile; do mongoimport --db metagenomic_db -c $jsonfile -file $jsonfile -type json; done
