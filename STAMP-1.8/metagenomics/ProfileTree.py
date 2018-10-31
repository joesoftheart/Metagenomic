'''
Stores hierarchical profile information for two or more samples.

@author: Donovan Parks
'''

from metagenomics.Profile import Profile, ProfileEntry

class Node:
  def __init__(self, name, parent = None):
    self.name = name    
    self.parent = parent
    self.children = []
    self.countData = []

  def depth(self):
    depth = 0
    curNode = self
    while curNode.parent != None:
      depth += 1
      curNode = curNode.parent
      
    return depth
  
  def isLeaf(self):
    return (len(self.children) == 0)
  
  def isRoot(self):
    return (self.parent == None)
  
  def childWithName(self, name):
    for child in self.children:
      if child.name == name:
        return child
      
    return None
  
class ProfileTree:
  def __init__(self):
    self.hierarchyHeadings = []
    self.sampleNames = []
    self.numSeqInSample = []
    
    self.root = Node('Entire sample')
    
  def numSamples(self):
    return len(self.sampleNames)
  
  def numSequencesInSample(self, name):
    index = self.sampleNames.index(name)
    return self.numSeqInSample[index]
  
  def numHierarchicalLevels(self):
    return len(self.hierarchyHeadings)
  
  def getHierarchicalLevelDepth(self, name):
    if name == 'Entire sample':
      return 0
    else:
      return self.hierarchyHeadings.index(name) + 1
    
  def getNodeWithName(self, node, name):
    if node.name == name:
      return self    
    elif node.isLeaf():
      return None
    
    for child in node.children:
      node = self.getNodeWidthName(child, name)
      if node != None:
        return node
      
    return None
  
  def getLeafNodes(self):
    curNode = self.root
    leafNodes = []
    for child in curNode.children:
      self.getLeafNodesRecursive(child, leafNodes)
        
    return leafNodes
  
  def getLeafNodesRecursive(self, node, leafNodes):
    if node.isLeaf():
      leafNodes.append(node)
      return
    
    for child in node.children:
      self.getLeafNodesRecursive(child, leafNodes)
  
  def createProfile(self, sampleName1, sampleName2, parentHeading, profileHeading):
    profile = Profile() 
    
    # get depth of hierarchical levels of interest 
    self.parentHeading = parentHeading
    self.profileHeading = profileHeading
    if parentHeading == 'Entire sample':
      parentDepth = 0
    else:
      parentDepth = self.hierarchyHeadings.index(parentHeading) + 1
      
    profileDepth = self.hierarchyHeadings.index(profileHeading) + 1
    
    profile.hierarchyHeadings = self.hierarchyHeadings[0:profileDepth]
    
    # get index for samples of interest
    sampleIndex1 = self.sampleNames.index(sampleName1)
    sampleIndex2 = self.sampleNames.index(sampleName2)
    profile.sampleNames = [sampleName1, sampleName2]
    
    # get all leaf nodes
    leafNodes = self.getLeafNodes()
    
    # traverse up tree from each leaf node      
    parentSeqDict = {} 
    for leaf in leafNodes:
      curDepth = len(self.hierarchyHeadings) 
      
      curNode = leaf      
      hierarchy = []
      while curNode != None:
        if not curNode.isRoot() and curDepth <= profileDepth:
          hierarchy.append(curNode.name)
        
        # add profile level information
        if curDepth == profileDepth:
          profileEntry = profile.profileDict.get(curNode.name)
          if profileEntry == None:
            profileEntry = ProfileEntry()
            profileEntry.featureCounts = [0, 0]
            profile.profileDict[curNode.name] = profileEntry
            
          profileEntry.featureCounts[0] += leaf.countData[sampleIndex1]
          profileEntry.featureCounts[1] += leaf.countData[sampleIndex2]
                  
        # add parent level information
        if curDepth == parentDepth:
          sequences = parentSeqDict.get(curNode.name)
          if sequences == None:
            sequences = [0, 0]
            parentSeqDict[curNode.name] = sequences
            
          sequences[0] += leaf.countData[sampleIndex1]
          sequences[1] += leaf.countData[sampleIndex2]
            
          profileEntry.parentCounts = sequences
            
        curDepth -= 1
        curNode = curNode.parent
    
      hierarchy.reverse()
      profileEntry.hierarchy = hierarchy      
      
    profile.numParentCategories = len(parentSeqDict)
    
    return profile
    
      
    
    