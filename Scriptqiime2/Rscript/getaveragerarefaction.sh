#!/bin/sh

Scriptqiime2/Rscript/averagecolumn.awk $1 | /bin/sed 's/^\t//' | /usr/bin/tr -s '\t' '\t' > $2
