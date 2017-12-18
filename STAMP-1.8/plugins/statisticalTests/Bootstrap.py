'''
Perform bootstrap test.

@author: Donovan Parks
'''

import mpmath
import random
from plugins.AbstractStatsTestPlugin import AbstractStatsTestPlugin

from metagenomics import mpmathSettings
mpmath.mp.dps = mpmathSettings.dps

from numpy.random import binomial

class Bootstrap(AbstractStatsTestPlugin):
  '''
  Perform bootstrap test.
  '''
  
  def __init__(self, preferences):
    AbstractStatsTestPlugin.__init__(self, preferences)
    self.name = 'Bootstrap'
  
  def hypothesisTest(self, seq1, seq2, totalSeq1, totalSeq2):
    replicates = 10000
    
    # create null distribution
    pooledN = totalSeq1 + totalSeq2
    pooledP = float(seq1 + seq2) / pooledN
    
    diff = []
    for dummy in xrange(0, replicates):
      c1 = binomial(totalSeq1, pooledP)         
      c2 = binomial(totalSeq2, pooledP)  
            
      diff.append(float(c1) / totalSeq1 - float(c2) / totalSeq2) 
      
    # determine number of replicates w/ an effect size more extreme than the observed data
    obsDiff = float(seq1) / totalSeq1 - float(seq2) / totalSeq2

    leftCount = 0
    rightCount = 0
    twoSidedCount = 0
    for value in diff:
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
  bootstrap = Bootstrap()
  pValueOneSided, pValueTwoSided = bootstrap.hypothesisTest(20, 1, 50, 50)
  print pValueOneSided, pValueTwoSided