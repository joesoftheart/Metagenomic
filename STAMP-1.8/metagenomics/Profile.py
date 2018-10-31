'''
Stores  profile information for two samples.

@author: Donovan Parks
'''

class ProfileEntry:
  def __init__(self):
    self.hierarchy = []
    self.featureCounts = []
    self.parentCounts = []
  
class Profile:
  def __init__(self):
    self.hierarchyHeadings = []
    self.sampleNames = []

    self.parentHeading = None
    self.profileHeading = None
    
    self.profileDict = {}
    
    self.numParentCategories = 0
        
  def getFeatures(self):
    return self.profileDict.keys()
    
  def getNumFeatures(self):
    return len(self.profileDict)
  
  def getNumParentCategories(self):
    return self.numParentCategories
    
  def getData(self, feature):
    return self.profileDict[feature]
    
  def getTableData(self, feature):
    data = self.profileDict[feature]
    seq1, seq2 = data.featureCounts
    parentSeq1, parentSeq2 = data.parentCounts
    return [seq1, seq2, parentSeq1, parentSeq2]
  
  def getLabeledTables(self):
    tables = [[feature] + self.getTableData(feature) for feature in self.profileDict.keys()]     
    return tables

  def getFeatureCounts(self, feature):
    return self.profileDict[feature].featureCounts
  
  def getParentCounts(self, feature):
    return self.profileDict[feature].parentCounts
  
  def getHierarchy(self, feature):
    return self.profileDict[feature].hierarchy
  
  def getSequenceCounts(self, sampleNum, bParentSeqCout = False):     
    data = []
    if bParentSeqCout:
      for feature in self.profileDict:
        data.append(self.profileDict[feature].parentCounts[sampleNum])
    else:
      for feature in self.profileDict:
        data.append(self.profileDict[feature].featureCounts[sampleNum])
           
    return data
    
    