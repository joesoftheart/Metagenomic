'''
Difference between proportions effect size measure.

@author: Donovan Parks
'''

from plugins.AbstractEffectSizePlugin import AbstractEffectSizePlugin

class DiffBetweenProp(AbstractEffectSizePlugin):
  
  def __init__(self, preferences):
    AbstractEffectSizePlugin.__init__(self, preferences)
    self.name = 'Difference between proportions'
    self.plotTitle = 'Difference between proportions (%)'
    self.bLogScale = False 

  def run(self, seq1, seq2, totalSeq1, totalSeq2):  
    p1 = float(seq1)/ max(totalSeq1, 1)
    p2 = float(seq2)/ max(totalSeq2, 1)
    return ( p1 - p2 ) * 100
  
if __name__ == "__main__": 
  pass