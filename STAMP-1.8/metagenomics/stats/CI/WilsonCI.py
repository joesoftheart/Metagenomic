'''
Wilson score confidence interval for a binomial proportion. Incorporates small probability modification proposed in
 "Interval Estimation for a Binomial Proportion" by Lawrence D. Brown, T. Tony Cai and Anirban DasGupta in Statistical Science,
 2001. 
 
Also see:
 "Two-sided confidence intervals for the single proportion: Comparison of seven methods"
   by Newcombe, R.G in STATISTICS IN MEDICINE, 1998
@author: Donovan Parks
'''

import mpmath

from metagenomics.stats.distributions.NormalDist import inverseNormalCDF

from scipy.stats import chi2

from metagenomics import mpmathSettings
mpmath.mp.dps = mpmathSettings.dps

class WilsonCI():
  
  def __init__(self):
    self.name = 'Wilson with Brown modification'
    
  def run(self, posSeqs, totalSeqs, coverage, zCoverage):  
    '''
     Calculate Wilson CI for a binomial distribution. Uses Brown correction
     for probabilities near zero. 
     
     posSeqs: number of positive sequences
     totalSeqs: total sequences drawn
     coverage: desired coverage
     zCoverage: standard normal z-value corresponding to desired coverage
     
     Note: clearly zCoverage could be calculates in this function. However, this calculation is expensive.
     To facilitate calling this function on several different binomial random variables this is taken as a
     parameter so it only needs to be calculated once.
     '''
     
    totalSeqs = max(totalSeqs, 1) 
    
    z = zCoverage
    zSqrd = z*z
                    
    p = mpmath.mpf(posSeqs) / totalSeqs
    q = 1.0 - p
    
    term1 = p + zSqrd / (2*totalSeqs)
    offset = z * mpmath.sqrt(p*q / totalSeqs + mpmath.mpf(zSqrd) / (4*totalSeqs*totalSeqs))
    denom = 1 + zSqrd / totalSeqs
    
    lowerCI = (term1 - offset) / denom
    upperCI = (term1 + offset) / denom
    
    if posSeqs >= 1 and posSeqs <=3:
      # use one-sided Poisson approximation when probability ~= 0 (see Brown et al., 2001)   
      lowerCI = 0.5*chi2.isf(coverage, 2*posSeqs) / totalSeqs
    
    return lowerCI, upperCI, p
  
if __name__ == "__main__": 
  wilsonCI = WilsonCI()
  lowerCI, upperCI, p = wilsonCI.run(10,100, 0.95, inverseNormalCDF(0.95))
  print lowerCI, upperCI, p
