'''
Append COG function categories to COG profiles from IMG/M

@author: Donovan Parks
'''

class COG_IO():
  def __init__(self):
    pass
  
  def appendCategories(self, inputFile, multiCogTreatment, outputFile):
    fin = open('data/fun.txt', 'U')
    funcData = fin.readlines()
    fin.close()
    
    # get COG hierarchy information
    funcDict = {}
    for line in funcData:
      line = line.strip()
      if len(line) == 0:
        continue
      
      if ':' in line:
        code = line[0]
        categoryName = line[2:]
        funcDict[code] = [curClass, categoryName] 
      else:
        # new functional classes
        curClass = line

    # get COG category for each COG
    fin = open('data/whog.txt', 'U')
    cogData = fin.readlines()
    fin.close()
    
    cogDict = {}
    for line in cogData:
      if '[' in line:
        # need to parse a new COG
        code = line[line.find('[')+1:line.find(']')]
        cogIndex = line.find('COG')
        cogId = line[cogIndex:cogIndex+7]    
        cogDict[cogId] = code

    # modify input file
    fin = open(inputFile, 'U')
    inputData = fin.readlines()
    fin.close()
    
    fout = open(outputFile, 'w')
        
    # write out header information
    headers = inputData[0].strip()
    headers = headers.split('\t')
    fout.write('COG classes' + '\t' + 'COG category names' + '\t' + 'COG category codes'
                + '\t' + 'COG annotations' + '\t' + 'COG IDs')
    
    for i in xrange(2,len(headers)):
      fout.write('\t' + headers[i])
    fout.write('\n')
    
    # write out each row
    for i in xrange(1, len(inputData)):
      line = inputData[i].strip()
      lineSplit = line.split('\t')
      cogId = lineSplit[0]
      cogAnnotation = lineSplit[1]
      cogCode = cogDict[cogId]
            
      if multiCogTreatment == 'Treat multi-code COGs as features':
        cogClass, cogCategoryName = funcDict.get(cogCode, [cogCode, cogCode])
        fout.write(cogClass + '\t' + cogCategoryName + '\t' + cogCode + '\t' + cogAnnotation + '\t' + cogId)
        for j in xrange(2, len(lineSplit)):
          fout.write('\t' + lineSplit[j])
        fout.write('\n')
      elif multiCogTreatment == 'Assign sequence to each COG code':
        for ch in cogCode:
          cogClass, cogCategoryName = funcDict.get(ch, [cogCode, cogCode])
          fout.write(cogClass + '\t' + cogCategoryName + '\t' + ch + '\t' + cogAnnotation + '\t' + cogId)
          for j in xrange(2, len(lineSplit)):
            fout.write('\t' + lineSplit[j])
          fout.write('\n')
        
    fout.close()
        
        
    
    