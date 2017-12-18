'''
Calculate coverage of a confidence interval.

@author: Donovan Parks
'''

import random
import math

from numpy.random import binomial

from metagenomics.MathHelper import mean, stdDev

class ConfIntervCoverage:
  
  def run(self, confIntervMethod, coverage, tables, trials, bootstrapRep, progress):
  
    tableData = []
    index = 0
    for row in tables:                    
      feature = row[0]
      seq1 = row[1]
      seq2 = row[2]
      parentSeq1 = row[3]
      parentSeq2 = row[4]
    
      lowerCI, upperCI, obsEffectSize = confIntervMethod.run(seq1, seq2, parentSeq1, parentSeq2, coverage) 
    
      p1 = float(seq1) / parentSeq1
      p2 = float(seq2) / parentSeq2
    
      coverageList = []  
      coverageListLess5 = []  
      coverageListGreater5 = []  
      for trial in xrange(0, trials): 
        if progress != '':
          index += 1
          progress.setValue(index)
          progress.setLabelText(feature + ' - Trial = ' + str(trial))  
          
        containedRep = 0
        for dummy in xrange(0, bootstrapRep):
          c1 = binomial(parentSeq1, p1)
          c2 = binomial(parentSeq2, p2)
      
          lowerCI, upperCI, effectSize = confIntervMethod.run(c1, c2, parentSeq1, parentSeq2, coverage)
          if obsEffectSize >= lowerCI and obsEffectSize <= upperCI:
            containedRep += 1        
               
        if min([seq1,seq2]) <= 5:
          coverageListLess5.append(float(containedRep) / bootstrapRep)
        else:
          coverageListGreater5.append(float(containedRep) / bootstrapRep)
          
        coverageList.append(float(containedRep) / bootstrapRep)
  
      row = []
      row.append(feature)
      row.append(seq1)
      row.append(seq2)
      row.append(parentSeq1)
      row.append(parentSeq2)
      row.append(float(seq1) / parentSeq1)
      row.append(float(seq2) / parentSeq2)
      row.append(mean(coverageList))
      row.append(stdDev(coverageList))
      
      if math.isnan(mean(coverageListLess5)):
        row.append('')
      else:
        row.append(mean(coverageListLess5))
        
      if math.isnan(stdDev(coverageListLess5)):
        row.append('')
      else:
        row.append(stdDev(coverageListLess5))
        
      if math.isnan(mean(coverageListGreater5)):
        row.append('')
      else:
        row.append(mean(coverageListGreater5))
        
      if math.isnan(stdDev(coverageListGreater5)):
        row.append('')
      else:
        row.append(stdDev(coverageListGreater5))

      tableData.append(row)
      
    return tableData

 
