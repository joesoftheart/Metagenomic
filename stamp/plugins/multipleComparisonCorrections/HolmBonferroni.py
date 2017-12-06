'''
Holm-Bonferroni multiple comparison correction.

@author: Donovan Parks
'''

from plugins.AbstractMultCompCorrection import AbstractMultCompCorrection

import mpmath
from metagenomics import mpmathSettings
mpmath.mp.dps = mpmathSettings.dps

class HolmBonferroni(AbstractMultCompCorrection):
  
  def __init__(self, preferences):
    AbstractMultCompCorrection.__init__(self, preferences)
    self.name = 'Holm-Bonferroni'
    self.method = 'Familywise error rate'
    self.bCorrectedValues = False
    self.numSignFeatures = 0
    
  def correct(self, pValues, alpha):
    # append an index identifier to each p-value
    indexedList = []
    index = 0
    for value in pValues:
      indexedList.append([value, index])
      index += 1
      
    # sort p-values in ascending order
    indexedList.sort()
    
    # determine significant features
    modifier = len(pValues)
    for i in xrange(0, len(indexedList)):
      index = indexedList[i][1]     
      if pValues[index] > alpha / modifier:
        nonSignIndex = i
        break
      modifier -= 1
      
    self.numSignFeatures = nonSignIndex
      
    for i in xrange(nonSignIndex, len(indexedList)):
      index = indexedList[i][1] 
      pValues[index] = float('inf')
  
    return pValues
 
  def additionalInfo(self):
    return [['Number of significant features', self.numSignFeatures]]
  
if __name__ == "__main__": 
  pass