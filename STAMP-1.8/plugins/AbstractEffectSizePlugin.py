'''
Abstract base class specifying an effect size filter.

@author: Donovan Parks
'''

class AbstractEffectSizePlugin:
  '''
  Abstract base class specifying an effect size filter.
  '''
  def __init__(self, preferences):
    self.preferences = preferences # dictionary indicating user-defined preferences
    self.name = 'Unnamed'         # name of filter
    self.plotTitle = 'Untitled'   # title to use in plots
    self.bLogScale = False        # indicate if effect size is returned in log space

  
  def run(self, seq1, seq2, totalSeq1, totalSeq2):
    '''
    Must return the effect size.
    '''
    pass
