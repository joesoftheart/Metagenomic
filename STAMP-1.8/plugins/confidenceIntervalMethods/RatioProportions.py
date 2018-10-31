'''
Calculate ratio of proportions (relative risk) confidence interval. 

@author: Donovan Parks
'''

import math
import mpmath

from plugins.AbstractConfIntervMethod import AbstractConfIntervMethod

from metagenomics.stats.distributions.NormalDist import inverseNormalCDF

from metagenomics import mpmathSettings
mpmath.mp.dps = mpmathSettings.dps

class RatioProportions(AbstractConfIntervMethod):
  
  def __init__(self, preferences):
    AbstractConfIntervMethod.__init__(self, preferences)
    self.name = 'RP: Asymptotic'
    self.plotLabel = 'Ratio of proportions'
    self.bRatio = True
    
  def run(self, seq1, seq2, totalSeq1, totalSeq2, coverage):
    '''
    Calculate ratio of proportions (relative risk) confidence interval. 
    '''
    if seq1 == 0 or seq2 == 0:
      pseudocount = self.preferences['Pseudocount']
      seq1 += pseudocount
      seq2 += pseudocount
      totalSeq1 += 2*pseudocount
      totalSeq2 += 2*pseudocount
      
    effectSize = (float(seq1) / totalSeq1) / (float(seq2) / totalSeq2)
    logEffectSize = math.log(effectSize)
    
    logSE = math.sqrt(1.0/seq1 - 1.0/totalSeq1 + 1.0/seq2 - 1.0/totalSeq2)
    
    zScore = inverseNormalCDF(coverage)
    logLowerCI = logEffectSize - zScore*logSE
    logUpperCI = logEffectSize + zScore*logSE
    
    lowerCI = math.exp(logLowerCI)
    upperCI = math.exp(logUpperCI)
    
    return lowerCI, upperCI, effectSize
  
if __name__ == "__main__": 
    ratioProp = RatioProportions()
    lowerCI, upperCI, effectSize = ratioProp.run(14, 17, 23, 19,0.05)
    print lowerCI
    print upperCI
    print effectSize