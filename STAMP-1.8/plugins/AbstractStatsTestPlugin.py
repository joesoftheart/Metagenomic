'''
Abstract base class specifying interface of a statistical hypothesis test.

@author: Donovan Parks
'''

class AbstractStatsTestPlugin:
  '''
  Abstract base class specifying interface of a statistical hypothesis test.
  '''
  def __init__(self, preferences):
    self.name = 'Unnamed'
  
  def hypothesisTest(self, seq1, seq2, totalSeq1, totalSeq2):
    '''
    Must return the one-sided and two-sided p-values for the hypothesis test.
    '''
    pass
  
  def power(self, seq1, seq2, totalSeq1, totalSeq2, alpha):
    '''
    Power of the statistical test. Return an empty list if the power cannot be calculated.
    '''
    return 'N/A'

  
  def equalSampleSize(self,seq1, seq2, totalSeq1, totalSeq2, alpha, beta):
    '''
    Equal sample size required to achieve a given power. Return an empty list if the power cannot be calculated.
    ''' 
    return 'N/A'