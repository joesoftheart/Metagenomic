'''
No multiple comparison correction.

@author: Donovan Parks
'''

from plugins.AbstractMultCompCorrection import AbstractMultCompCorrection

import mpmath
from metagenomics import mpmathSettings
mpmath.mp.dps = mpmathSettings.dps

class NoCorrection(AbstractMultCompCorrection):
  
  def __init__(self, preferences):
    AbstractMultCompCorrection.__init__(self, preferences)
    self.name = 'No correction'
    self.method = 'Per comparison error rate'
    self.bCorrectedValues = True
    self.numSignFeatures = 0
    
  def correct(self, pValues, alpha):
    self.numSignFeatures = len([x for x in pValues if x <= alpha])
    return pValues
 
  def additionalInfo(self):
    return [['Number of significant features', self.numSignFeatures]]
  
if __name__ == "__main__": 
  pass