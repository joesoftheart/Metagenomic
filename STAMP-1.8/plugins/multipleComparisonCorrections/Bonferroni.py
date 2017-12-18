'''
Bonferroni multiple comparison correction.

@author: Donovan Parks
'''

from plugins.AbstractMultCompCorrection import AbstractMultCompCorrection

import mpmath
from metagenomics import mpmathSettings
mpmath.mp.dps = mpmathSettings.dps

class Bonferroni(AbstractMultCompCorrection):
  
  def __init__(self, preferences):
    AbstractMultCompCorrection.__init__(self, preferences)
    self.name = 'Bonferroni'
    self.method = 'Familywise error rate'
    self.bCorrectedValues = True
    self.numSignFeatures = 0
    
    self.numComparisons = ''
    self.alpha = ''
    
  def correct(self, pValues, alpha):
    self.alpha = alpha
    self.numComparisons = len(pValues)
    
    corrected = []
    self.numSignFeatures = 0
    for pValue in pValues:
      correctedValue = pValue * self.numComparisons
      corrected.append(correctedValue)
      if correctedValue <= alpha:
        self.numSignFeatures += 1
      
    return corrected

  def additionalInfo(self):
    correctedErrorRate = self.alpha * (1.0/self.numComparisons)
    return [['Number of significant features', self.numSignFeatures],
            ['Corrected error rate', correctedErrorRate]]

  
if __name__ == "__main__": 
  pass