from Bio.Seq import Seq
import sys

input=sys.argv[1]	
dna = Seq(input)
print dna.reverse_complement()
