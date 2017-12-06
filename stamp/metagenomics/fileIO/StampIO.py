'''
Create a feature profile for a pair of metagenomic samples.

@author: Donovan Parks
'''

import string

from metagenomics.ProfileTree import ProfileTree, Node
from metagenomics.StringHelper import isNumber

class StampIO(object):
  def __init__(self, preferences):
    self.preferences = preferences
    
  def read(self, filename):      
    fin = open(filename, 'U')
    data = map(string.strip, fin.readlines())
    fin.close()
       
    profileTree = ProfileTree()
    
    # determine number of hierarchical levels and samples
    self.determineColumns(data, profileTree)
    
    if profileTree.numSamples() < 2:
      errMsg = 'Profile file must contain at least two samples.'
      return None, errMsg
    
    if profileTree.numHierarchicalLevels() == 0:
      errMsg = 'Profile file must a column indicating feature names.'
      return None, errMsg
    
    # construct profile tree
    profileTree.numSeqInSample = [0] * profileTree.numSamples()
    for i in xrange(1, len(data)):
      # ignore blank lines
      if data[i].strip() == "":
        continue

      lineSplit = data[i].split('\t')
      
      categories = lineSplit[0:profileTree.numHierarchicalLevels()]
      countData = [int(count) for count in lineSplit[profileTree.numHierarchicalLevels():]]
      
      # add all hierarchical levels
      curNode = profileTree.root
      for category in categories:
        node = curNode.childWithName(category)
        if node == None:
          node = Node(category, curNode)
          curNode.children.append(node)
          
        curNode = node
        
      # add count data to leaf node
      if curNode.countData == []:
        curNode.countData = countData
      else:
        for i in xrange(0, len(curNode.countData)):
          curNode.countData[i] += countData[i]
          
      # add count data to total sequence count
      for i in xrange(0, len(curNode.countData)):
        profileTree.numSeqInSample[i] += countData[i]
          
    return profileTree, None
        
  def determineColumns(self, data, profileTree):
    firstDataRow = data[1].split('\t')
    
    # first column entry that is numeric is assumed to be from first sample
    firstSampleIndex = 0
    for entry in firstDataRow:
      if isNumber(entry):
        break
      firstSampleIndex += 1
      
    # get hierarchical and sample names
    headings = data[0].split('\t')
    profileTree.hierarchyHeadings = headings[0:firstSampleIndex]
    profileTree.sampleNames = headings[firstSampleIndex:]
    
        
    
    
    