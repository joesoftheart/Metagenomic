'''
Calculate Spearman's rank correlation coefficient.

@author: Donovan Parks
'''

import math
import mpmath

from metagenomics import mpmathSettings
mpmath.mp.dps = mpmathSettings.dps

class Spearman():
  def __init__(self):
    self.name = 'Spearman\'s rank correlation coefficient'
    
  def rank(self, data):
    rank = {}
    
    n = len(data)
    data.sort()
    
    nextIndex = 0
    for i in xrange(0,n):  
      if i < nextIndex:
        continue
       
      # check for entries with same value
      end = i
      for j in xrange(i+1, n):
        if data[i] == data[j]:
          end = j
        else:
          break
        
      # assign rank to each entry with the same value
      curRank = 0.5*(end + i) + 1
      for j in xrange(i, end+1):
        rank[data[j]] = curRank
        
      # indicate next entry that needs to be processed
      nextIndex = end+1
      
    return rank    
    
  def compute(self, dataX, dataY):
    n = len(dataX)
    
    # determine rank of each dataset
    rankX = self.rank(list(dataX))  # must call with list() since we want to call-by-value
    rankY = self.rank(list(dataY))
   
    rank = []
    for i in xrange(0, n):
      rank.append([rankX[dataX[i]], rankY[dataY[i]]])
      
    # calculate Spearman's rank correlation coefficient    
    sumXY = 0
    sumX = 0
    sumY = 0
    sumXX = 0
    sumYY = 0
    for i in xrange(0, n):
      sumXY += rank[i][0]*rank[i][1]
      sumX += rank[i][0]
      sumY += rank[i][1]
      sumXX += rank[i][0]*rank[i][0]
      sumYY += rank[i][1]*rank[i][1]
      
    numerator = n*sumXY - sumX*sumY
    denominator = math.sqrt(n*sumXX - sumX*sumX) * math.sqrt(n*sumYY - sumY*sumY)
    Rs = numerator / denominator
    
    return Rs
  
if __name__ == "__main__": 
  spearman = Spearman()
  
  dataX = [106,86,100,101,99,103,97,113,112,110]
  dataY = [7,0,27,50,28,29,20,12,6,17]
  
  Rs = spearman.compute(dataX, dataY)
  print Rs