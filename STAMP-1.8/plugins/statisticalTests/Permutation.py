'''
Perform non-parametric permutation test.

@author: Donovan Parks
'''

import mpmath
import random
from plugins.AbstractStatsTestPlugin import AbstractStatsTestPlugin

from metagenomics import mpmathSettings
mpmath.mp.dps = mpmathSettings.dps

from numpy.random import hypergeometric

class Permutation(AbstractStatsTestPlugin):
  '''
  Perform bootstrap non-parametric statistical hypothesis test.
  '''
  
  def __init__(self, preferences):
    AbstractStatsTestPlugin.__init__(self, preferences)
    self.name = 'Permutation'
  
  def hypothesisTest(self, seq1, seq2, totalSeq1, totalSeq2):
    replicates = 10000
    
    # observed difference
    obsDiff = float(seq1) / totalSeq1 - float(seq2) / totalSeq2
    
    # randomly permute assignment of sequences
    permutationDiffs = []
    posSeq = seq1+seq2
    negSeq = totalSeq1+totalSeq2-posSeq
    for dummy in xrange(0, replicates):
      c1 = hypergeometric(posSeq, negSeq, totalSeq1)         
      c2 = posSeq - c1 
            
      permutationDiffs.append(float(c1) / totalSeq1 - float(c2) / totalSeq2) 
          
    # find p-value of permutation test (number of replicates with a value lower/greater than the observed value)
    leftCount = 0
    rightCount = 0
    twoSidedCount = 0
    for value in permutationDiffs:
      if value <= obsDiff:
        leftCount += 1
      if value >= obsDiff:
        rightCount += 1
      if abs(value) >= abs(obsDiff):
        twoSidedCount += 1
        
    oneSidedCount = leftCount
    if rightCount < oneSidedCount:
      oneSidedCount = rightCount
    
    return float(oneSidedCount) / replicates, float(twoSidedCount) / replicates
  
 
if __name__ == "__main__": 
  permutation = Permutation()
  pValueOneSided, pValueTwoSided = permutation.hypothesisTest(11, 11, 30, 60)
  print pValueOneSided
  print pValueTwoSided