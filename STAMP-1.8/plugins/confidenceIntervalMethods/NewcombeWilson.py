'''
Confidence interval method proposed by R. G. Newcombe in "Interval estimation for the difference
  between independent propotions: comparison of eleven methods", 1997.

@author: Donovan Parks
'''

import math
import mpmath

from plugins.AbstractConfIntervMethod import AbstractConfIntervMethod

from metagenomics.stats.distributions.NormalDist import inverseNormalCDF

from metagenomics import mpmathSettings
mpmath.mp.dps = mpmathSettings.dps

class NewcombeWilson(AbstractConfIntervMethod):
  
  def __init__(self, preferences):
    AbstractConfIntervMethod.__init__(self, preferences)
    self.name = 'DP: Newcombe-Wilson'
    self.plotLabel = 'Difference between proportions (%)'
    self.bRatio = False
  
  def NewcombeWilsonFindRoots(self, seq, totalSeq, z):
    '''
    Find roots required by Newcombe-Wilson CI method
    '''
    value = mpmath.mpf(0.0)
    stepSize = mpmath.mpf(1.0 / max(totalSeq,1000))
    steps = int(1.0 / stepSize)
    prevP = z*math.sqrt(value*(1.0-value) / totalSeq) - abs(value - mpmath.mpf(seq) / totalSeq)
    prevValue = value
    roots = []
    for dummy in xrange(0,steps):
      p = z*math.sqrt(value*(1.0-value) / totalSeq) - abs(value - mpmath.mpf(seq) / totalSeq)
      if p*prevP < 0 or (p == 0 and value == 0) or (p == 0 and value == 1.0):
        # we have found a root since there is a sign change
        if abs(p)+abs(prevP) != 0:
          root = prevValue + stepSize*(1.0 - (abs(p)/(abs(p)+abs(prevP))))
        else:
          root = prevValue
        roots.append(root)
        
        if len(roots) == 2:
          break
      
      prevP = p
      prevValue = value  
      value += stepSize
    
    # check if we have a double root
    if len(roots) == 1:
      roots.append(roots[0])
    
    return roots
  
  def run(self, seq1, seq2, totalSeq1, totalSeq2, coverage):
    '''
    Calculate confidence interval using Newcombe-Wilson method.
      Results are report as percent difference.
    '''
    
    if totalSeq1 == 0:
      totalSeq1 = 1
      
    if totalSeq2 == 0:
      totalSeq2 = 1
    
    zScore = inverseNormalCDF(coverage)
    
    roots1 = self.NewcombeWilsonFindRoots(seq1, totalSeq1, zScore)
    roots2 = self.NewcombeWilsonFindRoots(seq2, totalSeq2, zScore)
  
    diff = float(seq1)/totalSeq1 - float(seq2)/totalSeq2
    lowerCI = zScore*math.sqrt(roots1[0]*(1-roots1[0])/totalSeq1 + roots2[1]*(1-roots2[1])/totalSeq2)
    upperCI = zScore*math.sqrt(roots1[1]*(1-roots1[1])/totalSeq1 + roots2[0]*(1-roots2[0])/totalSeq2)
    
    return (diff-lowerCI)*100, (diff+upperCI)*100, diff*100
  
if __name__ == "__main__": 
  pass