#!/bin/sh

#get line based on column which have the value >= 0.01 or 1% relative abundance in L6 (genus level)

/usr/bin/awk '{for(i=2;i<=NF;i++)if($i >= 0.01){print $0; next}}' $1 > $2