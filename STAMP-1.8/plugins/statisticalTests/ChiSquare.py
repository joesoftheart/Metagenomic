'''
Perform Chi-square statistical hypothesis test

@author: Donovan Parks
'''

import math
import mpmath
from plugins.AbstractStatsTestPlugin import AbstractStatsTestPlugin
from metagenomics.stats.distributions.NormalDist import standardNormalCDF, inverseNormalCDF

from scipy.stats import chi2

from metagenomics import mpmathSettings
mpmath.mp.dps = mpmathSettings.dps

class ChiSquare(AbstractStatsTestPlugin):
  '''
 Perform Chi-square statistical hypothesis test
  '''
  
  def __init__(self, preferences):
    AbstractStatsTestPlugin.__init__(self, preferences)
    self.name = 'Chi-square test'
  
  def hypothesisTest(self, seq1, seq2, totalSeq1, totalSeq2):
    # Contingency table:
    # x1 x2
    # y1 y2
    x1 = seq1
    x2 = seq2
    y1 = totalSeq1 - x1
    y2 = totalSeq2 - x2
    
    if (x1 == 0 and x2 == 0) or (x1 == totalSeq1 or x2 == totalSeq2):
      return float('inf'), 1.0
    
    N = x1+x2+y1+y2
    
    E00 = float((x1+x2) * (x1+y1)) / N
    E01 = float((x1+x2) * (x2+y2)) / N
    E10 = float((y1+y2) * (x1+y1)) / N
    E11 = float((y1+y2) * (x2+y2)) / N
  
    X2 = (abs(x1 - E00))**2 / E00
    X2 += (abs(x2 - E01))**2 / E01
    X2 += (abs(y1 - E10))**2 / E10
    X2 += (abs(y2 - E11))**2 / E11
    
    # calculate p-value
    pValueTwoSided = 1.0 - chi2.cdf(X2,1)
  
    return float('inf'), pValueTwoSided
  
  def power(self, seq1, seq2, totalSeq1, totalSeq2, alpha): 
    # The chi-square test is equivalent to the difference between proportions
    # test as illustrated by Rivals et al., 2007. Here we use the standard
    # asymptotic power formulation for a difference between proportions test.
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
    # The chi-square test is equivalent to the difference between proportions
    # test as illustrated by Rivals et al., 2007. Here we use the standard
    # equal sample size formulation for a difference between proportions test.
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
  chiSquare = ChiSquare()
  pValueOne, pValueTwo = chiSquare.hypothesisTest(10, 20, 60, 50)
  print pValueOne
  print pValueTwo
