'''
Asymptotic confidence interval with continuity correction often used with a difference between proportions test.

@author: Donovan Parks
'''

import math
import mpmath

from plugins.AbstractConfIntervMethod import AbstractConfIntervMethod

from metagenomics.stats.distributions.NormalDist import inverseNormalCDF

from metagenomics import mpmathSettings
mpmath.mp.dps = mpmathSettings.dps

class DiffBetweenPropAsymptoticCC(AbstractConfIntervMethod):
  
  def __init__(self, preferences):
    AbstractConfIntervMethod.__init__(self, preferences)
    self.name = 'DP: Asymptotic-CC'
    self.plotLabel = 'Difference between proportions (%)'
    self.bRatio = False

  def run(self, seq1, seq2, totalSeq1, totalSeq2, coverage):
    '''
    Calculate confidence interval using asymptotic method with a continuity correction.
      Results are report as percent difference.
    '''
    if totalSeq1 == 0:
      totalSeq1 = 1
      
    if totalSeq2 == 0:
      totalSeq2 = 1
      
    R1 = mpmath.mpf(seq1) / totalSeq1
    R2 = mpmath.mpf(seq2) / totalSeq2
  
    diff = R1 - R2
    stdErr = math.sqrt((R1*(1-R1)) / totalSeq1 + (R2*(1-R2)) / totalSeq2) + (1.0/totalSeq1 + 1.0/totalSeq2)/2
    offset = inverseNormalCDF(coverage) * stdErr
  
    return (diff - offset) * 100, (diff + offset) * 100, diff * 100
  
if __name__ == "__main__": 
  pass