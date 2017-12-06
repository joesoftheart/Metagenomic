'''
Odds ratio effect size measure.

@author: Donovan Parks
'''

from plugins.AbstractEffectSizePlugin import AbstractEffectSizePlugin

class OddsRatio(AbstractEffectSizePlugin):
  
  def __init__(self, preferences):
    AbstractEffectSizePlugin.__init__(self, preferences)
    self.name = 'Odds ratio'
    self.plotTitle = 'Odds ratio'
    self.bLogScale = True 

  def run(self, seq1, seq2, totalSeq1, totalSeq2):  
    a = seq1
    b = seq2
    c = totalSeq1 - seq1
    d = totalSeq2 - seq2
    
    if a == 0 or b == 0 or c == 0 or d == 0:
      pseudocount = self.preferences['Pseudocount']
      a += pseudocount
      b += pseudocount
      c += pseudocount
      d += pseudocount

    return (float(a) * d) / (b * c)
  
if __name__ == "__main__": 
  pass