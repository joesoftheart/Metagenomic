awk -F"\t" -v OFS="\t" '
  NR==1{$3=""; print; next;}
  {
  c[$2]++; 
  for (i=4;i<=NF;i++) {
    s[$2"."i]+=$i};
  } 
  END {
    for (k in c) {
      printf "%s\t", k; 
      for(i=4;i<NF;i++) printf "%.1f\t", s[k"."i]/c[k]; 
      printf "%.1f\n", s[k"."NF]/c[k];
    }
  }' $1
