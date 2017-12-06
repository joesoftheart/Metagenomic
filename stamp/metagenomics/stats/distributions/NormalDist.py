'''
Created on Oct 2, 2009

@author: Donovan Parks
'''

import mpmath
from metagenomics import mpmathSettings
mpmath.mp.dps = mpmathSettings.dps
             
def standardNormalCDF(z):
  '''
  Standard normal cumulative distribution function
  '''  
  return mpmath.ncdf(z)

def inverseNormalCDF(area):
  return mpmath.erfinv(area) * mpmath.sqrt(2.0)

if __name__ == "__main__": 
  pass