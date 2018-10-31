'''
Sidak multiple comparison correction.

@author: Donovan Parks
'''

from plugins.AbstractMultCompCorrection import AbstractMultCompCorrection

import mpmath
from metagenomics import mpmathSettings
mpmath.mp.dps = mpmathSettings.dps

class Sidak(AbstractMultCompCorrection):
  
  def __init__(self, preferences):
    AbstractMultCompCorrection.__init__(self, preferences)
    self.name = 'Sidak'
    self.method = 'Familywise error rate'
    self.bCorrectedValues = True
    self.numSignFeatures = 0
    
  def correct(self, pValues, alpha):
    self.alpha = alpha
    self.numComparisons = len(pValues)
    
    corrected = []
    for pValue in pValues:
      correctedValue = 1.0 - (1.0 - pValue)**self.numComparisons
      corrected.append(correctedValue)
      if correctedValue <= alpha:
        self.numSignFeatures += 1
      
    return corrected

  def additionalInfo(self):
    correctedErrorRate = 1.0 - (1.0 - self.alpha)**(1.0/self.numComparisons)
    return [['Number of significant features', self.numSignFeatures],
            ['Corrected error rate', correctedErrorRate]]
  
if __name__ == "__main__": 
  pass