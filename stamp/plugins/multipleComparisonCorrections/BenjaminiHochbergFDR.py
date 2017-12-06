'''
Benjamini-Hochberg false discovery rate method.

Benjamini Y and Hochberg R. (1995) Controlling the False Discovery Rate: a Practical and Powerful
Approach to Multiple Testing. Journal of the Royal Statistical Society (Series B), Vol. 57, No. 1, pp. 289-300. 

@author: Donovan Parks
'''

from plugins.AbstractMultCompCorrection import AbstractMultCompCorrection

import mpmath
from metagenomics import mpmathSettings
mpmath.mp.dps = mpmathSettings.dps

class BenjaminiHochbergFDR(AbstractMultCompCorrection):
  
  def __init__(self, preferences):
    AbstractMultCompCorrection.__init__(self, preferences)
    self.name = 'Benjamini-Hochberg FDR'
    self.method = 'False discovery rate'
    self.bCorrectedValues = True
    self.numSignFeatures = 0
    
  def correct(self, pValues, alpha):
    # append an index identifier to each p-value
    indexedList = []
    index = 0
    for value in pValues:
      indexedList.append([value, index])
      index += 1
      
    # sort p-values in descending order
    indexedList.sort(reverse = True)
    
    # determine significant features
    numComparisons = len(pValues)
    modifier = numComparisons
    self.numSignFeatures = 0
    for i in xrange(0, len(indexedList)):
      index = indexedList[i][1]     
      
      pValues[index] = pValues[index] * numComparisons / float(modifier)
      if pValues[index] < alpha:
        self.numSignFeatures += 1
        
      modifier -= 1
  
    return pValues
  
  def additionalInfo(self):
    return [['Number of significant features', self.numSignFeatures]]
  
if __name__ == "__main__": 
  pass