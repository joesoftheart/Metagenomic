'''
Abstract base class specifying interface of a confidence interval method.

@author: Donovan Parks
'''

class AbstractConfIntervMethod:
  '''
  Abstract base class specifying interface of a confidence interval method.
  '''
  def __init__(self, preferences):
    self.preferences = preferences # dictionary indicating user-defined preferences
    self.name = 'Unnamed'
    self.plotLabel = 'No plot label defined'
    self.bRatio = False       # indicate if effect size statistic is a ratio (imples skewed distribution)
    
  
  def run(self, seq1, seq2, totalSeq1, totalSeq2, coverage):
    '''
    Must return the lower and upper values of the confidence interval along with the effect size.
    '''
    pass
