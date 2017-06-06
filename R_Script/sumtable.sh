# ./sumtable.sh listdupingenuscal.txt
awk  -F"\t" '       {B[$1]++
                 for (i=2; i<=NF; i++) A[$1, i]+=$i
                 N=NF}
         END    {for (j in B) {printf "%s", j
                         for (k=2; k<=N; k++) printf "%s","\t"A[j,k]
                         printf "\n"
                        }
                }
        ' $1
