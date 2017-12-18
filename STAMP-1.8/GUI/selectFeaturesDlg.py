'''
Dialog box used to select features which should be ignored.

@author: Donovan Parks
'''

from PyQt4 import QtGui, QtCore
from selectFeaturesUI import Ui_SelectFeatureDlg

class SelectFeaturesDlg(QtGui.QDialog):
  def __init__(self, statsTestResults, parent=None):
    QtGui.QWidget.__init__(self, parent)
    
    # initialize GUI
    self.ui = Ui_SelectFeatureDlg()
    self.ui.setupUi(self)  
    
    self.selectedFeatures = []
    self.inactiveFeatures = []  
    
    self.init(statsTestResults)
    
    # setup signals
    self.connect(self.ui.btnFilterFeature, QtCore.SIGNAL('clicked()'), self.filterFeature)
    self.connect(self.ui.btnAddFeature, QtCore.SIGNAL('clicked()'), self.addFeature)
    self.connect(self.ui.cboHierarchicalLevel, QtCore.SIGNAL('activated(QString)'), self.hierarchyChanged)

    self.centerWindow()
      
  def init(self, statsTestResults):
    if statsTestResults.profile == None:
      return
    
    self.statsTestResults = statsTestResults
    
    # initialize hierarchy
    self.ui.cboHierarchicalLevel.clear()
    for heading in self.statsTestResults.profile.hierarchyHeadings:
      self.ui.cboHierarchicalLevel.addItem(heading)
    self.ui.cboHierarchicalLevel.setCurrentIndex(len(self.statsTestResults.profile.hierarchyHeadings)-1)
    
    # initialize active and filtered features
    allFeatures = set(statsTestResults.getColumn('Features', False))
    self.selectedFeatures = set(statsTestResults.getSelectedFeatures())
    self.inactiveFeatures = list(allFeatures - self.selectedFeatures)
    self.selectedFeatures = list(self.selectedFeatures)
    
    for feature in self.selectedFeatures:
      self.ui.lstSelectedFeatures.addItem(feature)
      
    for feature in self.inactiveFeatures:
      self.ui.lstFilteredFeatures.addItem(feature)
      
    self.ui.txtNumSelectedFeatures.setText(str(len(self.selectedFeatures)))
      
  def getSelectedFeatures(self):
    return self.selectedFeatures
    
  def hierarchyChanged(self):
    index = self.ui.cboHierarchicalLevel.currentIndex()
    
    # modify active list
    dict = {}
    for feature in self.selectedFeatures:
      headings = self.statsTestResults.profile.getHierarchy(feature)
      selectedFeature = headings[index]
      dict[selectedFeature] = dict.get(selectedFeature, 0) + 1

    self.ui.lstSelectedFeatures.clear()
    for feature in dict:
      if index == len(self.statsTestResults.profile.hierarchyHeadings) - 1:
        self.ui.lstSelectedFeatures.addItem(feature)
      else:
        self.ui.lstSelectedFeatures.addItem(feature + ' [' + str(dict[feature]) + ']')
    
    # modify filtered list
    dict = {}
    for feature in self.inactiveFeatures:
      headings = self.statsTestResults.profile.getHierarchy(feature)
      selectedFeature = headings[index]
      dict[selectedFeature] = dict.get(selectedFeature, 0) + 1
      
    self.ui.lstFilteredFeatures.clear()
    for feature in dict:
      if index == len(self.statsTestResults.profile.hierarchyHeadings) - 1:
        self.ui.lstFilteredFeatures.addItem(feature)
      else:
        self.ui.lstFilteredFeatures.addItem(feature + ' [' + str(dict[feature]) + ']')
        
    self.ui.txtNumSelectedFeatures.setText(str(len(self.selectedFeatures)))
        
  def filterFeature(self):
    items = self.ui.lstSelectedFeatures.selectedItems()
    for item in items:     
      itemText = str(item.text())
      pos = itemText.find(' [') 
      if pos != -1:
        itemText = itemText[0:pos]
        
      for feature in reversed(self.selectedFeatures):
        headings = self.statsTestResults.profile.getHierarchy(feature)
        if itemText in headings:
          self.selectedFeatures.remove(feature)
          self.inactiveFeatures.append(feature)
          
    self.hierarchyChanged()
  
  def addFeature(self):
    items = self.ui.lstFilteredFeatures.selectedItems()
    for item in items:     
      itemText = str(item.text())
      pos = itemText.find(' [') 
      if pos != -1:
        itemText = itemText[0:pos]

      for feature in reversed(self.inactiveFeatures):
        headings = self.statsTestResults.profile.getHierarchy(feature)
        if itemText in headings:
          self.selectedFeatures.append(feature)
          self.inactiveFeatures.remove(feature)

    self.hierarchyChanged()

  def centerWindow(self):
    screen = QtGui.QDesktopWidget().screenGeometry()
    size =  self.geometry()
    self.move((screen.width()-size.width())/2, (screen.height()-size.height())/2)