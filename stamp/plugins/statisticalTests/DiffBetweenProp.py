'''
Perform difference between proportions statistical hypothesis test.

@author: Donovan Parks
'''

import math
import mpmath
from plugins.AbstractStatsTestPlugin import AbstractStatsTestPlugin
from metagenomics.stats.distributions.NormalDist import standardNormalCDF, inverseNormalCDF

from metagenomics import mpmathSettings
mpmath.mp.dps = mpmathSettings.dps

class DiffBetweenProp(AbstractStatsTestPlugin):
  '''
  Perform difference between proportions statistical hypothesis test.
  '''
  
  def __init__(self, preferences):
    AbstractStatsTestPlugin.__init__(self, preferences)
    self.name = 'Difference between proportions'
  
  def hypothesisTest(self, seq1, seq2, totalSeq1, totalSeq2):
    R1 = mpmath.mpf(seq1) / totalSeq1
    R2 = mpmath.mpf(seq2) / totalSeq2
    diff = R1 - R2
    P = mpmath.mpf(seq1 + seq2) / (totalSeq1 + totalSeq2)
    Q = 1.0 - P
    
    if (seq1 == 0 and seq2 == 0) or (seq1 == totalSeq1 and seq2 == totalSeq2):
      D = 0
    else:    
      D = diff / math.sqrt(P*Q*((1.0/totalSeq1) + (1.0/totalSeq2)))
    
    # calculate one-sided and two-sided p-value
    ZScore = abs(D)
    pValueOneSided = standardNormalCDF(ZScore)
    if pValueOneSided > 0.5:
      pValueOneSided = 1.0 - pValueOneSided
    pValueTwoSided = 2*pValueOneSided
  
    return pValueOneSided, pValueTwoSided
  
  def power(self, seq1, seq2, totalSeq1, totalSeq2, alpha): 
    oneMinusAlpha = 1.0 - alpha
     
    p1 = float(seq1) / totalSeq1
    p2 = float(seq2) / totalSeq2
    d = p1 - p2

    stdDev = math.sqrt( (p1 * (1-p1)) / totalSeq1 + (p2 * (1 - p2)) / totalSeq2 )
    
    if stdDev != 0:    
      p = float(totalSeq1*p1 + totalSeq2*p2) / (totalSeq1 + totalSeq2)
      q = 1-p
      pooledStdDev = math.sqrt( (p*q) / totalSeq1 + (p*q) / totalSeq2 )
      
      zScore = inverseNormalCDF(oneMinusAlpha)
      zLower = ( -zScore * pooledStdDev - d ) / stdDev
      zUpper= ( zScore * pooledStdDev - d ) / stdDev
    
      return standardNormalCDF(zLower) + (1.0 - standardNormalCDF(zUpper))
    else:
      return 1.0
  
  
  def equalSampleSize(self, seq1, seq2, totalSeq1, totalSeq2, alpha, beta):
    oneMinusAlpha = 1.0 - alpha
    oneMinusBeta = 1.0 - beta
    
    p1 = float(seq1) / totalSeq1
    p2 = float(seq2) / totalSeq2
    q1 = 1.0 - p1
    q2 = 1.0 - p2
    d = p1 - p2
    
    if d == 0:
      return 1  

    return (inverseNormalCDF(oneMinusAlpha) * math.sqrt((p1 + p2)*(q1 + q2)/2) + inverseNormalCDF(oneMinusBeta)*math.sqrt((p1*q1) + (p2*q2)))**2 / (d**2)


if __name__ == "__main__": 
  diffBetweenProp = DiffBetweenProp()
  pValueOne, pValueTwo = diffBetweenProp.hypothesisTest(23, 10, 13221, 2317)
  print pValueOne
  print pValueTwo