'''
Perform Chi-square statistical hypothesis test (w/ Yates' correction)

@author: Donovan Parks
'''

import mpmath
from plugins.AbstractStatsTestPlugin import AbstractStatsTestPlugin

from scipy.stats import chi2

from metagenomics import mpmathSettings
mpmath.mp.dps = mpmathSettings.dps

class ChiSquareYates(AbstractStatsTestPlugin):
  '''
 Perform Chi-square statistical hypothesis test (w/ Yates' correction)
  '''
  
  def __init__(self, preferences):
    AbstractStatsTestPlugin.__init__(self, preferences)
    self.name = 'Chi-square test (w/ Yates\' correction)'
  
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
  
    X2 = (abs(x1 - E00)-0.5)**2 / E00
    X2 += (abs(x2 - E01)-0.5)**2 / E01
    X2 += (abs(y1 - E10)-0.5)**2 / E10
    X2 += (abs(y2 - E11)-0.5)**2 / E11
    
    # calculate p-value
    pValueTwoSided = 1.0 - chi2.cdf(X2,1)
  
    return float('inf'), pValueTwoSided

if __name__ == "__main__": 
  chiSquareYates = ChiSquareYates()
  pValueOne, pValueTwo = chiSquareYates.hypothesisTest(10, 20, 60, 50)
  print pValueOne
  print pValueTwo
