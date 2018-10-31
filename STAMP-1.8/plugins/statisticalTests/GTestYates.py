'''
Perform G-test statistical hypothesis test (w/ Yates' correction)

@author: Donovan Parks
'''

import math
import mpmath
from plugins.AbstractStatsTestPlugin import AbstractStatsTestPlugin

from scipy.stats import chi2

from metagenomics import mpmathSettings
mpmath.mp.dps = mpmathSettings.dps

class GTestYates(AbstractStatsTestPlugin):
  '''
  Perform G-test statistical hypothesis test (w/ Yates' correction)
  '''
  
  def __init__(self, preferences):
    AbstractStatsTestPlugin.__init__(self, preferences)
    self.name = 'G-test (w/ Yates\' correction)'
  
  def hypothesisTest(self, seq1, seq2, totalSeq1, totalSeq2):
    # Contingency table:
    # x1 x2
    # y1 y2
    x1 = seq1
    x2 = seq2
    y1 = totalSeq1 - x1
    y2 = totalSeq2 - x2
    
    # perform Yates' correction
    if x1*y2 - x2*y1 > 0:
      x1 -= 0.5
      y2 -= 0.5
      x2 += 0.5
      y1 += 0.5
    else:
      x1 += 0.5
      y2 += 0.5
      x2 -= 0.5
      y1 -= 0.5
    
    # calculate g-test statistic
    N = x1+x2+y1+y2
  
    E00 = float((x1+x2) * (x1+y1)) / N
    E01 = float((x1+x2) * (x2+y2)) / N
    E10 = float((y1+y2) * (x1+y1)) / N
    E11 = float((y1+y2) * (x2+y2)) / N
  
    gTest = 0
    
    if (x1 > 0 and E00 > 0):
      gTest = x1 * math.log(x1/E00)
      
    if (x2 > 0 and E01 > 0):
      gTest += x2 * math.log(x2/E01)
      
    if (y1 > 0 and E10 > 0):
      gTest += y1 * math.log(y1/E10)
      
    if (y2 > 0 and E11 > 0):
      gTest += y2 * math.log(y2/E11)
      
    gTest = 2*gTest
    
    # calculate p-value
    pValueTwoSided = 1.0 - chi2.cdf(gTest,1)
  
    return float('inf'), pValueTwoSided

if __name__ == "__main__": 
  gTest = GTestYates()
  pValueOne, pValueTwo = gTest.hypothesisTest(10, 20, 60, 50)
  print pValueOne
  print pValueTwo
