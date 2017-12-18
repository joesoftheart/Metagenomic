'''
Ratio of proportions effect size measure.

@author: Donovan Parks
'''

from plugins.AbstractEffectSizePlugin import AbstractEffectSizePlugin

class RatioProportions(AbstractEffectSizePlugin):
  
  def __init__(self, preferences):
    AbstractEffectSizePlugin.__init__(self, preferences)
    self.name = 'Ratio of proportions'
    self.plotTitle = 'Ratio of proportions'
    self.bLogScale = True 
    
  def run(self, seq1, seq2, totalSeq1, totalSeq2):  
    if seq1 == 0 or seq2 == 0:
      pseudocount = self.preferences['Pseudocount']
      seq1 += pseudocount
      seq2 += pseudocount
      totalSeq1 += pseudocount
      totalSeq2 += pseudocount
      
    return (float(seq1) / totalSeq1) / (float(seq2) / totalSeq2)
  
if __name__ == "__main__": 
  pass