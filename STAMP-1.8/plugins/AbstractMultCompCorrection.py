'''
Abstract base class specifying interface of a multiple comparison correction method.

@author: Donovan Parks
'''

class AbstractMultCompCorrection:
  '''
  Abstract base class specifying interface of a multiple comparison correction method.
  '''
  def __init__(self, preferences):
    self.name = 'Unnamed'
    self.method = 'Not specified' # should be set to 'Familywise error rate' or 'False discovery rate'
    self.bCorrectedValues = False # indicates if a method produces a list of corrected values (True) or
                                  # only a set of significant features (False)
                                  
  def correct(self, pValues, alpha):
    '''
    Must return the corrected p-values.
    '''
    pass
  
  def additionalInfo(self):
    '''
    Return any additional information regarding a multiple comparison method. Information must
     be returned as a list of lists specifying the name and value of each additional piece of information.
     
      e.g., info = [['Label 1', 7],['Label 2', 0.33],['Label 3','Yes']]
    '''
    return []