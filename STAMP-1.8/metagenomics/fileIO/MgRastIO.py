'''
Create profile from MG-RAST phylogenetic or metabolic file.

@author: Donovan Parks
'''

class MgRastIO():
  def __init__(self):
    pass
  
  def parseMetabolicFile(self, filename, profileCol, parentCol):
    '''
    Parse MG-RAST metabolic file.
      filename: name of file to parse
      profileCol: column containing features to create profile for
      parentCol: column containing parental features/group in hierarchy
    ''' 
    fin = open(filename, 'U')
    data = fin.readlines()
    fin.close()
       
    # get number of sequences in each profile and parental feature
    profileDict = {}
    parentDict = {}
    profileHierarchyDict = {}
    totalSeq = 0
    for i in xrange(1, len(data)):
      lineSplit = data[i].split('\t')
          
      # replace blank feature names by the MRA with a defined name
      for j in xrange(1, profileCol+1):
        if lineSplit[j] == '':
          lineSplit[j] = lineSplit[j-1]
          
      profileFeature = lineSplit[profileCol].strip()
      parentFeature = lineSplit[parentCol].strip()
      numSeq = int(lineSplit[-1].strip())
      
      parentDict[parentFeature] = parentDict.get(parentFeature, 0) + numSeq
      profileDict[profileFeature] = profileDict.get(profileFeature, 0) + numSeq
      profileHierarchyDict[profileFeature] = lineSplit[0:profileCol]
      
      totalSeq += numSeq
       
    return profileDict, parentDict, profileHierarchyDict, totalSeq
  
  def parsePhylogeneticFile(self, filename, heading):
    '''
    Parse MG-RAST phylogenetic file.
      filename: name of file to parse
      heading: heading of hierarchical level to create profile from
    ''' 
    profile = {}
    
    return profile