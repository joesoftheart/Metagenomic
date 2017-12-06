'''
Calculate odds ratio confidence interval. 

@author: Donovan Parks
'''

import math
import mpmath

from plugins.AbstractConfIntervMethod import AbstractConfIntervMethod

from metagenomics.stats.distributions.NormalDist import inverseNormalCDF

from metagenomics import mpmathSettings
mpmath.mp.dps = mpmathSettings.dps

class OddsRatio(AbstractConfIntervMethod):
  
  def __init__(self, preferences):
    AbstractConfIntervMethod.__init__(self, preferences)
    self.name = 'OR: Haldane adjustment'
    self.plotLabel = 'Odds ratio'
    self.bRatio = True
    
  def tableValues(self, seq1, seq2, totalSeq1, totalSeq2):
    a = seq1
    b = seq2
    c = totalSeq1 - seq1
    d = totalSeq2 - seq2
    
    # boundary correction (Haldane, 1956 modification; see Agresti, Biometrics 1999)
    if a == 0 or b == 0 or c == 0 or d == 0:
      pseudocount = self.preferences['Pseudocount']
      a += pseudocount
      b += pseudocount
      c += pseudocount
      d += pseudocount
      
    return a, b, c, d
    
  def run(self, seq1, seq2, totalSeq1, totalSeq2, coverage):
    '''
    Calculate odds ratio confidence interval. 
    '''
    a, b, c, d = self.tableValues(seq1, seq2, totalSeq1, totalSeq2)
    
    effectSize = (float(a) * d) / (float(b) * c)
    logEffectSize = math.log(effectSize)
    
    logSE = math.sqrt(1.0/a + 1.0/b + 1.0/c + 1.0/d)
    
    zScore = inverseNormalCDF(coverage)
    logLowerCI = logEffectSize - zScore*logSE
    logUpperCI = logEffectSize + zScore*logSE
    
    lowerCI = math.exp(logLowerCI)
    upperCI = math.exp(logUpperCI)
    
    return lowerCI, upperCI, effectSize
  
if __name__ == "__main__": 
  oddsRatio = OddsRatio()
  lowerCI, upperCI, effectSize = oddsRatio.run(141,420,928+141,13525+420,0.05)
  print lowerCI
  print upperCI
  print effectSize