#!/bin/sh

 cat $1*.assembled.fastq | awk '{if (NR%4==2) sum+=length ($1)} END {print "Number of Read in fastq = "NR/4"\t"" Average of read length in fastq = "sum/(NR/4)}'
