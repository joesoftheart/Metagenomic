'''
Calculate power of a hypothesis test.

@author: Donovan Parks
'''

import random
import math

from metagenomics.MathHelper import mean, stdDev

class Power:
  
  def run(self, test, signLevel, statsResults, trials, bootstrapRep, progress):
     
    tableData = []
    index = 0
    for row in statsResults:                    
      feature = row[0]
      seq1 = row[1]
      seq2 = row[2]
      parentSeq1 = row[3]
      parentSeq2 = row[4]

      p1 = float(seq1) / parentSeq1
      p2 = float(seq2) / parentSeq2
    
      powerList = []  
      powerListLess5 = []  
      powerListGreater5 = []  
      for trial in xrange(0, trials): 
        if progress != '':
          index += 1
          progress.setValue(index)
          progress.setLabelText(feature + ' - Trial = ' + str(trial))   
          
        power = 0
        processedReplicates = 0
        for dummy in xrange(0, bootstrapRep):
          c1 = 0
          c2 = 0
          for dummy in xrange(0, parentSeq1):
            rnd = random.random()
            if rnd <= p1:
              c1 += 1
              
          for dummy in xrange(0, parentSeq2):
            rnd = random.random()
            if rnd <= p2:
              c2 += 1
      
          if c1 == 0 and c2 == 0:
            # This is a special case that many hypothesis test will not handle correctly
            # so we just ignore it. This will have little effect on the calculated power
            # of a test.
            continue
          
          processedReplicates += 1
          
          pValueOneSided, pValueTwoSided = test.hypothesisTest(c1, c2, parentSeq1, parentSeq2)
          if pValueTwoSided < signLevel:
            power += 1      
               
        if processedReplicates > 0:
          if min([seq1,seq2]) <= 5:
            powerListLess5.append(float(power) / processedReplicates)
          else:
            powerListGreater5.append(float(power) / processedReplicates)
            
          powerList.append(float(power) / processedReplicates)
  
      row = []
      row.append(feature)
      row.append(seq1)
      row.append(seq2)
      row.append(parentSeq1)
      row.append(parentSeq2)
      row.append(float(seq1) / parentSeq1)
      row.append(float(seq2) / parentSeq2)
      row.append(mean(powerList))
      row.append(stdDev(powerList))
      
      if math.isnan(mean(powerListLess5)):
        row.append('')
      else:
        row.append(mean(powerListLess5))
        
      if math.isnan(stdDev(powerListLess5)):
        row.append('')
      else:
        row.append(stdDev(powerListLess5))
        
      if math.isnan(mean(powerListGreater5)):
        row.append('')
      else:
        row.append(mean(powerListGreater5))
        
      if math.isnan(stdDev(powerListGreater5)):
        row.append('')
      else:
        row.append(stdDev(powerListGreater5))

      tableData.append(row)
      
    return tableData

 
