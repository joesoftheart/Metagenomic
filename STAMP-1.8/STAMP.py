import sys, os
import string

# If running this from a mac app, add the app's libraries and files to the path:
if len(sys.argv) == 2 and sys.argv[1].startswith("--macapp"):
  sys.path.insert(0,sys.path[0]+"/../Resources")

from PyQt4 import QtGui, QtCore

from mainUI import Ui_MainWindow
from GUI.selectFeaturesDlg import SelectFeaturesDlg
from GUI.createProfileDlg import CreateProfileDlg
from GUI.assignCOGsDlg import AssignCOGsDlg
from GUI.preferencesDlg import PreferencesDlg
from GUI.multCompCorrectionInfoDlg import MultCompCorrectionInfoDlg 

from commandLine import CommandLineParser

from metagenomics.stats.StatsTests import StatsTests
from metagenomics.fileIO.StampIO import StampIO
from metagenomics.GenericTable import GenericTable
from metagenomics.stats.empiricalTests.ConfIntervCoverage import ConfIntervCoverage
from metagenomics.stats.empiricalTests.Power import Power
from metagenomics.ProfileTree import ProfileTree
from metagenomics.Profile import Profile

from metagenomics.DirectoryHelper import getMainDir

import plugins.Dependencies
from plugins.PlotsManager import PlotsManager
from plugins.PluginManager import PluginManager
 
class MainWindow(QtGui.QMainWindow):
  def __init__(self, preferences, parent=None):
    QtGui.QWidget.__init__(self, parent)
    
    self.preferences = preferences
           
    # initialize GUI
    self.ui = Ui_MainWindow()
    self.ui.setupUi(self)

    self.lastDirectory = ''
    
    # load plot plugins
    self.exploratoryPlots = PlotsManager(self.ui.cboExploratoryPlots, self.ui.plotExploratoryScrollArea, 'Profile scatter plot')
    self.exploratoryPlots.loadPlots(self.preferences, 'plugins/exploratoryPlots/')
  
    self.statPlots = PlotsManager(self.ui.cboStatPlots, self.ui.plotStatScrollArea, 'Extended error bar')
    self.statPlots.loadPlots(self.preferences, 'plugins/statPlots/')
    
    # load statistical technique plugins
    pluginManager = PluginManager(self.preferences)
    self.effectSizeDict = pluginManager.loadPlugins('plugins/effectSizeFilters/')
    pluginManager.populateComboBox(self.effectSizeDict, self.ui.cboEffectSizeMeasure1, 'Difference between proportions')
    pluginManager.populateComboBox(self.effectSizeDict, self.ui.cboEffectSizeMeasure2, 'Ratio of proportions')
    
    self.statTestDict = pluginManager.loadPlugins('plugins/statisticalTests/')
    pluginManager.populateComboBox(self.statTestDict, self.ui.cboStatTests, 'Fisher\'s exact test')
    
    self.multCompDict = pluginManager.loadPlugins('plugins/multipleComparisonCorrections/')
    pluginManager.populateComboBox(self.multCompDict, self.ui.cboMultCompMethod, 'No correction')
    
    self.confIntervMethodDict = pluginManager.loadPlugins('plugins/confidenceIntervalMethods/')
    pluginManager.populateComboBox(self.confIntervMethodDict, self.ui.cboConfIntervMethods, 'DP: Newcombe-Wilson')
                    
    # initialize class variables   
    self.profileTree = ProfileTree()   
    self.profile = Profile()
    self.statsTest = StatsTests()
    self.plotWindows = []
            
    # initialize tables
    self.summaryTable = GenericTable([], [], self)
    self.coverageTable = GenericTable([], [], self)
    self.powerTable = GenericTable([], [], self)
                 
    # connect menu items signals to slots
    self.connect(self.ui.mnuFileOpenProfile, QtCore.SIGNAL('triggered()'), self.loadProfile)   
    self.connect(self.ui.mnuFileCreateProfile, QtCore.SIGNAL('triggered()'), self.createProfile)
    self.connect(self.ui.mnuFileAppendCategoryCOG, QtCore.SIGNAL('triggered()'), self.appendCategoriesCOG)
    self.connect(self.ui.mnuFileSavePlot, QtCore.SIGNAL('triggered()'), self.saveImageDlg)
    self.connect(self.ui.mnuFileSaveTable, QtCore.SIGNAL('triggered()'), self.saveTableDlg)
    self.connect(self.ui.mnuFileExit, QtCore.SIGNAL('triggered()'), QtCore.SLOT('close()'))
       
    self.connect(self.ui.mnuViewSendPlotToWindow, QtCore.SIGNAL('triggered()'), self.sendPlotToWindow)
    
    self.connect(self.ui.mnuSettingsPreferences, QtCore.SIGNAL('triggered()'), self.prefrencesDlg)
        
    self.connect(self.ui.mnuHelpAbout, QtCore.SIGNAL('triggered()'), self.openAboutDlg)   
    
    # widget controls in sidebar
    self.connect(self.ui.btnProfileTab, QtCore.SIGNAL('clicked()'), self.profileTabClicked)  
    self.connect(self.ui.btnProfileArrow, QtCore.SIGNAL('clicked()'), self.profileTabClicked)   
    self.connect(self.ui.btnStatisticsTab, QtCore.SIGNAL('clicked()'), self.statPropTabClicked)   
    self.connect(self.ui.btnStatisticsArrow, QtCore.SIGNAL('clicked()'), self.statPropTabClicked)  
    self.connect(self.ui.btnFilteringTab, QtCore.SIGNAL('clicked()'), self.filteringTabClicked)   
    self.connect(self.ui.btnFilteringArrow, QtCore.SIGNAL('clicked()'), self.filteringTabClicked)  
    
    # connect profile widget signals to slots
    self.connect(self.ui.cboSample1, QtCore.SIGNAL('activated(QString)'), self.hierarchicalLevelsChanged)
    self.connect(self.ui.cboSample2, QtCore.SIGNAL('activated(QString)'), self.hierarchicalLevelsChanged)
    self.connect(self.ui.btnSample1Colour, QtCore.SIGNAL('clicked()'), self.sample1ColourDlg)
    self.connect(self.ui.btnSample2Colour, QtCore.SIGNAL('clicked()'), self.sample2ColourDlg)
    self.connect(self.ui.cboProfileLevel, QtCore.SIGNAL('activated(QString)'), self.profileLevelChanged)
    self.connect(self.ui.cboParentalLevel, QtCore.SIGNAL('activated(QString)'), self.parentLevelChanged)
    
    # connect statistical test widget signals to slots   
    self.connect(self.ui.cboStatTests, QtCore.SIGNAL('activated(QString)'), self.statTestPropChanged)  
    self.connect(self.ui.cboSignTestType, QtCore.SIGNAL('activated(QString)'), self.statTestPropChanged)
    self.connect(self.ui.cboConfIntervMethods, QtCore.SIGNAL('activated(QString)'), self.statTestPropChanged)  
    self.connect(self.ui.cboNominalCoverage, QtCore.SIGNAL('activated(QString)'), self.statTestPropChanged)    
    self.connect(self.ui.btnRunTest, QtCore.SIGNAL('clicked()'), self.runTest)
    self.connect(self.ui.cboMultCompMethod, QtCore.SIGNAL('activated(QString)'), self.multCompCorrectionChanged)
    self.connect(self.ui.btnMultCompCorrectionInfo, QtCore.SIGNAL('clicked()'), self.multCompCorrectionInfo) 
    
    # connect filtering test widget signals to slots   
    self.connect(self.ui.chkSelectFeatures, QtCore.SIGNAL('toggled(bool)'), self.selectFeaturesCheckbox)
    self.connect(self.ui.btnSelectFeatures, QtCore.SIGNAL('clicked()'), self.selectFeaturesDlg)
        
    self.connect(self.ui.chkEnableSignLevelFilter, QtCore.SIGNAL('toggled(bool)'), self.filteringPropChanged)
    self.connect(self.ui.spinSignLevelFilter, QtCore.SIGNAL('valueChanged(QString)'), self.filteringPropChanged)
     
    self.connect(self.ui.cboSeqFilter, QtCore.SIGNAL('activated(QString)'), self.seqFilterChanged)
    self.connect(self.ui.chkEnableSeqFilter, QtCore.SIGNAL('toggled(bool)'), self.filteringPropChanged)
    self.connect(self.ui.spinFilterSample1, QtCore.SIGNAL('valueChanged(QString)'), self.filteringPropChanged)
    self.connect(self.ui.spinFilterSample2, QtCore.SIGNAL('valueChanged(QString)'), self.filteringPropChanged)
    
    self.connect(self.ui.cboParentSeqFilter, QtCore.SIGNAL('activated(QString)'), self.parentSeqFilterChanged)
    self.connect(self.ui.chkEnableParentSeqFilter, QtCore.SIGNAL('toggled(bool)'), self.filteringPropChanged)
    self.connect(self.ui.spinParentFilterSample1, QtCore.SIGNAL('valueChanged(QString)'), self.filteringPropChanged)
    self.connect(self.ui.spinParentFilterSample2, QtCore.SIGNAL('valueChanged(QString)'), self.filteringPropChanged)
    
    self.connect(self.ui.radioOR, QtCore.SIGNAL('clicked()'), self.filteringPropChanged)
    self.connect(self.ui.radioAND, QtCore.SIGNAL('clicked()'), self.filteringPropChanged)

    self.connect(self.ui.cboEffectSizeMeasure1, QtCore.SIGNAL('activated(QString)'), self.changeEffectSizeMeasure)
    self.connect(self.ui.cboEffectSizeMeasure2, QtCore.SIGNAL('activated(QString)'), self.changeEffectSizeMeasure)
    self.connect(self.ui.spinMinEffectSize1, QtCore.SIGNAL('valueChanged(QString)'), self.filteringPropChanged)
    self.connect(self.ui.spinMinEffectSize2, QtCore.SIGNAL('valueChanged(QString)'), self.filteringPropChanged)
    self.connect(self.ui.chkEnableEffectSizeFilter1, QtCore.SIGNAL('toggled(bool)'), self.filteringPropChanged)
    self.connect(self.ui.chkEnableEffectSizeFilter2, QtCore.SIGNAL('toggled(bool)'), self.filteringPropChanged)
        
    self.connect(self.ui.btnApplyFilters, QtCore.SIGNAL('clicked()'), self.applyFilters)
                   
    # connect exploratory plot page widget signals to slots
    self.connect(self.ui.cboExploratoryPlots, QtCore.SIGNAL('activated(QString)'), self.exploratoryPlotUpdate)
    self.connect(self.ui.btnExploratoryConfigurePlot, QtCore.SIGNAL('clicked()'), self.exploratoryPlotConfigure)
      
    # connect statistical plot page widget signals to slots
    self.connect(self.ui.cboStatPlots, QtCore.SIGNAL('activated(QString)'), self.statPlotUpdate)
    self.connect(self.ui.btnStatConfigurePlot, QtCore.SIGNAL('clicked()'), self.statPlotConfigure)
    self.connect(self.ui.cboHighlightHierarchyExploratory, QtCore.SIGNAL('activated(QString)'), self.highlightHierarchyExploratoryChanged)
    self.connect(self.ui.cboHighlightFeatureExploratory, QtCore.SIGNAL('activated(QString)'), self.highlightFeatureExploratoryChanged)
    self.connect(self.ui.cboHighlightHierarchyStats, QtCore.SIGNAL('activated(QString)'), self.highlightHierarchyStatsChanged)
    self.connect(self.ui.cboHighlightFeatureStats, QtCore.SIGNAL('activated(QString)'), self.highlightFeatureStatsChanged)

    # connect paired profile page widget signals to slots
    self.connect(self.ui.chkShowActiveFeatures, QtCore.SIGNAL('toggled(bool)'), self.populateSummaryTable)

    # connect CI coverage page widget signals to slots
    self.connect(self.ui.btnConfIntervCoverage, QtCore.SIGNAL('clicked()'), self.confIntervCoverage)
    
    # connect CI coverage page widget signals to slots
    self.connect(self.ui.btnPowerTest, QtCore.SIGNAL('clicked()'), self.powerTest)
    
    # initialize dynamic GUI elements
    self.seqFilterChanged()
    self.parentSeqFilterChanged()
    self.setSample1Colour(self.preferences['Sample 1 colour'])
    self.setSample2Colour(self.preferences['Sample 2 colour'])

    # show window maximized 
    self.resize(1100, 700)
    self.showMaximized()  
    
  def appendCategoriesCOG(self):
    assignCOGsDlg = AssignCOGsDlg(self)     
    assignCOGsDlg.exec_()
    
  def profileTabClicked(self):
    self.ui.widgetProfile.setVisible(not self.ui.widgetProfile.isVisible())
    self.updateSideBarTabIcon(self.ui.widgetProfile, self.ui.btnProfileArrow)
    
  def statPropTabClicked(self): 
    self.ui.widgetStatisticalProp.setVisible(not self.ui.widgetStatisticalProp.isVisible())
    self.updateSideBarTabIcon(self.ui.widgetStatisticalProp, self.ui.btnStatisticsArrow)
    
  def filteringTabClicked(self): 
    self.ui.widgetFilter.setVisible(not self.ui.widgetFilter.isVisible())
    self.updateSideBarTabIcon(self.ui.widgetFilter, self.ui.btnFilteringArrow)
    
  def updateSideBarTabIcon(self, tab, arrowButton):
    icon = QtGui.QIcon()
    if tab.isVisible():    
      icon.addPixmap(QtGui.QPixmap("icons/downArrow.png"), QtGui.QIcon.Normal, QtGui.QIcon.Off)
    else:
      icon.addPixmap(QtGui.QPixmap("icons/rightArrow.png"), QtGui.QIcon.Normal, QtGui.QIcon.Off)
    arrowButton.setIcon(icon)
       
  def prefrencesDlg(self):
    preferencesDlg = PreferencesDlg(self)    
     
    preferencesDlg.ui.spinPseudoCount.setValue(self.preferences['Pseudocount'])
    preferencesDlg.ui.chkTruncateFeatureNames.setChecked(self.preferences['Truncate feature names'])
    preferencesDlg.ui.spinFeatureNameLength.setValue(self.preferences['Length of truncated feature names'])
    
    if preferencesDlg.exec_() == QtGui.QDialog.Accepted:
      self.preferences['Pseudocount'] = preferencesDlg.ui.spinPseudoCount.value()
      self.preferences['Truncate feature names'] = preferencesDlg.ui.chkTruncateFeatureNames.isChecked()
      self.preferences['Length of truncated feature names'] = preferencesDlg.ui.spinFeatureNameLength.value()
      
    self.exploratoryPlotUpdate()
    self.statPlotUpdate()
      
  def exploratoryPlotUpdate(self):
    QtGui.QApplication.instance().setOverrideCursor(QtGui.QCursor(QtCore.Qt.WaitCursor))
    self.exploratoryPlots.update(self.profile)
    QtGui.QApplication.instance().restoreOverrideCursor()
    
  def exploratoryPlotConfigure(self):
    if self.profile.getNumFeatures() != 0:
      self.exploratoryPlots.configure(self.profile)
    else:
      QtGui.QMessageBox.information(self, 'Invalid profile', 'A profile must be loaded before plots can be configured.', QtGui.QMessageBox.Warning)
           
  def statPlotUpdate(self):
    QtGui.QApplication.instance().setOverrideCursor(QtGui.QCursor(QtCore.Qt.WaitCursor))
    self.statPlots.update(self.statsTest.results)
    QtGui.QApplication.instance().restoreOverrideCursor()
    
  def statPlotConfigure(self):
    if self.statsTest.results.profile != None:
      self.statPlots.configure(self.statsTest.results)
    else:
      QtGui.QMessageBox.information(self, 'Invalid profile', 'Statistical test must be run before plots can be configured.', QtGui.QMessageBox.Warning)
            
  def sample1ColourDlg(self):
    colour = QtGui.QColorDialog.getColor(self.preferences['Sample 1 colour'], self, 'Colour for sample 1')
    
    if colour.isValid():
      self.preferences['Sample 1 colour'] = colour
      self.setSample1Colour(colour)
      
  def setSample1Colour(self, colour):
    colourStr = str(colour.red()) + ',' + str(colour.green()) + ',' + str(colour.blue())
    self.ui.btnSample1Colour.setStyleSheet('* { background-color: rgb(' + colourStr + ') }')
    self.exploratoryPlotUpdate()
    self.statPlotUpdate()
    
  def sample2ColourDlg(self):
    colour = QtGui.QColorDialog.getColor(self.preferences['Sample 2 colour'], self, 'Colour for sample 2')

    if colour.isValid():
      self.preferences['Sample 2 colour'] = colour
      self.setSample2Colour(colour)
  
  def setSample2Colour(self, colour):
    colourStr = str(colour.red()) + ',' + str(colour.green()) + ',' + str(colour.blue())
    self.ui.btnSample2Colour.setStyleSheet('* { background-color: rgb(' + colourStr + ') }')
    self.exploratoryPlotUpdate()
    self.statPlotUpdate()
                
  def createProfile(self):
    createProfileDlg = CreateProfileDlg(self)     
    createProfileDlg.exec_()
           
  def loadProfile(self):   
    # open file dialog
    file = QtGui.QFileDialog.getOpenFileName(self, 'Open profile', self.lastDirectory, 'STAMP profile file (*.spf *.txt);;All files (*.*)')
    if file == '':
      return

    self.lastDirectory = file[0:file.lastIndexOf('/')]

    # read profiles from file  
    try:
      stampIO = StampIO(self.preferences)
      self.profileTree, errMsg = stampIO.read(file)
      
      if errMsg != None:
        QtGui.QMessageBox.information(self, 'Error reading input file', errMsg, QtGui.QMessageBox.Warning)
        return
    
    except: 
      QtGui.QMessageBox.information(self, 'Error reading input file','Unknown parsing error.', QtGui.QMessageBox.Warning)
      return
    
    QtGui.QApplication.instance().setOverrideCursor(QtGui.QCursor(QtCore.Qt.WaitCursor))
    
    # populate sample combo boxes
    self.ui.cboSample1.clear()
    self.ui.cboSample2.clear()
    for name in self.profileTree.sampleNames:  
      self.ui.cboSample1.addItem(name)
      self.ui.cboSample2.addItem(name)  
    self.ui.cboSample1.setCurrentIndex(self.ui.cboSample1.findText(self.profileTree.sampleNames[0]))
    self.ui.cboSample2.setCurrentIndex(self.ui.cboSample2.findText(self.profileTree.sampleNames[1]))
    
    # populate hierarchy combo boxes
    self.ui.cboParentalLevel.clear()
    self.ui.cboParentalLevel.addItem('Entire sample') 
    for header in self.profileTree.hierarchyHeadings[0:-1]:
      self.ui.cboParentalLevel.addItem(header)      
    self.ui.cboParentalLevel.setCurrentIndex(0)
    
    self.ui.cboProfileLevel.clear()
    for header in self.profileTree.hierarchyHeadings:
      self.ui.cboProfileLevel.addItem(header)      
    self.ui.cboProfileLevel.setCurrentIndex(0)
                        
    # indicate the hierarchical level of interest have changed
    self.hierarchicalLevelsChanged()
          
    QtGui.QApplication.instance().restoreOverrideCursor()
      
  def parentLevelChanged(self):
    parentDepth = self.profileTree.getHierarchicalLevelDepth(str(self.ui.cboParentalLevel.currentText()))
    profileDepth= self.profileTree.getHierarchicalLevelDepth(str(self.ui.cboProfileLevel.currentText()))
    
    if parentDepth >= profileDepth:
      QtGui.QMessageBox.information(self, 'Invalid profile', 'The parent level must be higher in the hierarchy than the profile level.', QtGui.QMessageBox.Warning)
      self.ui.cboParentalLevel.setCurrentIndex(0)
      return
    
    self.hierarchicalLevelsChanged()      
    
  def profileLevelChanged(self):
    parentDepth = self.profileTree.getHierarchicalLevelDepth(str(self.ui.cboParentalLevel.currentText()))
    profileDepth= self.profileTree.getHierarchicalLevelDepth(str(self.ui.cboProfileLevel.currentText()))
    
    if profileDepth <= parentDepth:
      QtGui.QMessageBox.information(self, 'Invalid profile', 'The profile level must be deeper in the hierarchy than the parent level.', QtGui.QMessageBox.Warning)
      self.ui.cboProfileLevel.setCurrentIndex(len(self.profileTree.hierarchyHeadings)-1)
      return
    
    self.hierarchicalLevelsChanged()
          
  def hierarchicalLevelsChanged(self):   
    QtGui.QApplication.instance().setOverrideCursor(QtGui.QCursor(QtCore.Qt.WaitCursor))
          
    # indicate that profile information has changed
    refreshIcon = QtGui.QIcon()
    refreshIcon.addPixmap(QtGui.QPixmap("icons/refresh.png"), QtGui.QIcon.Normal, QtGui.QIcon.Off)
    self.ui.btnRunTest.setIcon(refreshIcon)
    
    # create new profile
    sampleName1 = str(self.ui.cboSample1.currentText())
    sampleName2 = str(self.ui.cboSample2.currentText())
    
    parentHeading = str(self.ui.cboParentalLevel.currentText())
    profileHeading = str(self.ui.cboProfileLevel.currentText())
    
    self.profile = self.profileTree.createProfile(sampleName1, sampleName2, parentHeading, profileHeading)
    
    # update GUI to reflect new profile
    self.ui.txtTotalSeqs1.setText(str(self.profileTree.numSequencesInSample(sampleName1)))
    self.ui.txtTotalSeqs2.setText(str(self.profileTree.numSequencesInSample(sampleName2)))
    
    self.ui.txtNumParentCategories.setText(str(self.profile.getNumParentCategories()))
    self.ui.txtNumFeatures.setText(str(self.profile.getNumFeatures()))
    
    self.ui.cboHighlightHierarchyStats.setCurrentIndex(0)
    self.ui.cboHighlightHierarchyExploratory.setCurrentIndex(0)
    
    # populate highlight hierarchy combo box
    profileIndex = self.profileTree.hierarchyHeadings.index(profileHeading)
    self.ui.cboHighlightHierarchyStats.clear()
    self.ui.cboHighlightHierarchyExploratory.clear()
    self.ui.cboHighlightHierarchyStats.addItem('None') 
    self.ui.cboHighlightHierarchyExploratory.addItem('None') 
    for header in self.profileTree.hierarchyHeadings[0:profileIndex+1]:  
      self.ui.cboHighlightHierarchyStats.addItem(header)  
      self.ui.cboHighlightHierarchyExploratory.addItem(header)  
    self.ui.cboHighlightHierarchyStats.setCurrentIndex(0)
    self.ui.cboHighlightHierarchyExploratory.setCurrentIndex(0)
    
    self.ui.cboHighlightFeatureStats.clear()
    self.ui.cboHighlightFeatureExploratory.clear()   
    
    # indicate that any previously calculated results, plots, or tables are now invalid
    self.exploratoryPlots.reset(self.preferences)
    self.statPlots.reset(self.preferences)
    
    selectedFeatures = self.statsTest.results.getSelectedFeatures()
    self.statsTest = StatsTests()
    self.statsTest.results.setSelectedFeatures(selectedFeatures)
    
    self.statPlotUpdate()
    self.updateFilterInfo()
    self.populateSummaryTable() 
    
    self.exploratoryPlotUpdate()
    
    QtGui.QApplication.instance().restoreOverrideCursor()
            
  def runTest(self): 
    QtGui.QApplication.instance().setOverrideCursor(QtGui.QCursor(QtCore.Qt.WaitCursor))
    
    # indicate data is up-to-date
    noIcon = QtGui.QIcon()
    self.ui.btnRunTest.setIcon(noIcon)
    
    # show progress of test
    progress = QtGui.QProgressDialog("Running statistical test...", QtCore.QString(), 0, len(self.profile.getFeatures()), self)
    progress.setWindowTitle('Progress')
    progress.setWindowModality(QtCore.Qt.WindowModal)
    
    # run significance test
    test =  self.statTestDict[str(self.ui.cboStatTests.currentText())]
    testType = str(self.ui.cboSignTestType.currentText())    
    confIntervMethod = self.confIntervMethodDict[str(self.ui.cboConfIntervMethods.currentText())]
    coverage = float(self.ui.cboNominalCoverage.currentText())                           
    self.statsTest.run(test, testType, confIntervMethod, coverage, self.profile, progress)
    
    # apply multiple test correction
    multCompClass = self.multCompDict[str(self.ui.cboMultCompMethod.currentText())]
    self.statsTest.results.performMultCompCorrection(multCompClass)
   
    # apply filters
    self.applyFilters()
    
    QtGui.QApplication.instance().restoreOverrideCursor() 
    
  def statTestPropChanged(self):
    refreshIcon = QtGui.QIcon()
    refreshIcon.addPixmap(QtGui.QPixmap("icons/refresh.png"), QtGui.QIcon.Normal, QtGui.QIcon.Off)
    self.ui.btnRunTest.setIcon(refreshIcon)
    
  def multCompCorrectionChanged(self):    
    multCompClass = self.multCompDict[str(self.ui.cboMultCompMethod.currentText())]
    if multCompClass.method == 'False discovery rate':
      self.ui.lblSignLevelFilter.setText('q-value filter (>):')
    else:
      self.ui.lblSignLevelFilter.setText('p-value filter (>):')
    self.statTestPropChanged()
    
  def multCompCorrectionInfo(self):
    if self.statsTest.results.multCompCorrection != None:
      multCompDlg = MultCompCorrectionInfoDlg(self, self.statsTest.results.multCompCorrection.additionalInfo())
      multCompDlg.exec_()    
    else:
      QtGui.QMessageBox.information(self, 'Run test', 'Run hypothesis test first.', QtGui.QMessageBox.Ok)
      
  def seqFilterChanged(self):
    if self.ui.cboSeqFilter.currentText() == 'maximum':
      self.ui.lblSeqFilterSample1.setText('Maximum (<):')
      
    elif self.ui.cboSeqFilter.currentText() == 'minimum':
      self.ui.lblSeqFilterSample1.setText('Minimum (<):')
      
    elif self.ui.cboSeqFilter.currentText() == 'independent':
      self.ui.lblSeqFilterSample1.setText('Sample 1 (<):')
      
    self.filteringPropChanged()
    
  def parentSeqFilterChanged(self):
    if self.ui.cboParentSeqFilter.currentText() == 'maximum':
      self.ui.lblParentSeqFilterSample1.setText('Maximum (<):')
      
    elif self.ui.cboParentSeqFilter.currentText() == 'minimum':
      self.ui.lblParentSeqFilterSample1.setText('Minimum (<):')
      
    elif self.ui.cboParentSeqFilter.currentText() == 'independent':
      self.ui.lblParentSeqFilterSample1.setText('Sample 1 (<):')
      
    self.filteringPropChanged()
    
  def changeEffectSizeMeasure(self):
    self.filteringPropChanged()

  def filteringPropChanged(self):   
    # indicate that profile information has changed
    refreshIcon = QtGui.QIcon()
    refreshIcon.addPixmap(QtGui.QPixmap("icons/refresh.png"), QtGui.QIcon.Normal, QtGui.QIcon.Off)
    self.ui.btnApplyFilters.setIcon(refreshIcon)
    
    self.ui.btnSelectFeatures.setEnabled(self.ui.chkSelectFeatures.isChecked())
    
    self.ui.spinSignLevelFilter.setEnabled(self.ui.chkEnableSignLevelFilter.isChecked())
    
    self.ui.cboSeqFilter.setEnabled(self.ui.chkEnableSeqFilter.isChecked())
    self.ui.spinFilterSample1.setEnabled(self.ui.chkEnableSeqFilter.isChecked())
    self.ui.spinFilterSample2.setEnabled(self.ui.chkEnableSeqFilter.isChecked() and self.ui.cboSeqFilter.currentText() == 'independent')
    self.ui.lblSeqFilterSample2.setEnabled(self.ui.cboSeqFilter.currentText() == 'independent')
    
    self.ui.cboParentSeqFilter.setEnabled(self.ui.chkEnableParentSeqFilter.isChecked())
    self.ui.spinParentFilterSample1.setEnabled(self.ui.chkEnableParentSeqFilter.isChecked())
    self.ui.spinParentFilterSample2.setEnabled(self.ui.chkEnableParentSeqFilter.isChecked() and self.ui.cboParentSeqFilter.currentText() == 'independent')
    self.ui.lblParentSeqFilterSample2.setEnabled(self.ui.cboParentSeqFilter.currentText() == 'independent')
    
    self.ui.cboEffectSizeMeasure1.setEnabled(self.ui.chkEnableEffectSizeFilter1.isChecked())
    self.ui.spinMinEffectSize1.setEnabled(self.ui.chkEnableEffectSizeFilter1.isChecked())
    
    self.ui.cboEffectSizeMeasure2.setEnabled(self.ui.chkEnableEffectSizeFilter2.isChecked())
    self.ui.spinMinEffectSize2.setEnabled(self.ui.chkEnableEffectSizeFilter2.isChecked())
    
  def selectFeaturesCheckbox(self):
    self.filteringPropChanged()
    
  def selectFeaturesDlg(self):
    selectFeatureDialog = SelectFeaturesDlg(self.statsTest.results, self)
            
    if selectFeatureDialog.exec_() == QtGui.QDialog.Accepted:   
      selectedFeatures = selectFeatureDialog.getSelectedFeatures()                    
      self.statsTest.results.setSelectedFeatures(selectedFeatures)      
      self.filteringPropChanged()

  def applyFilters(self):   
    QtGui.QApplication.instance().setOverrideCursor(QtGui.QCursor(QtCore.Qt.WaitCursor))
    icon = QtGui.QIcon()
    self.ui.btnApplyFilters.setIcon(icon)
    
    if not self.ui.chkSelectFeatures.isChecked():
      self.statsTest.results.selectAllFeautres()
    
    # perform filtering
    signLevelFilter = self.ui.spinSignLevelFilter.value()
    if not self.ui.chkEnableSignLevelFilter.isChecked():
      signLevelFilter = None
    
    # sequence filtering
    seqFilter = str(self.ui.cboSeqFilter.currentText())
    sample1Filter = int(self.ui.spinFilterSample1.value())
    sample2Filter = int(self.ui.spinFilterSample2.value())
    if not self.ui.chkEnableSeqFilter.isChecked():
      seqFilter = None
      sample1Filter = None
      sample2Filter = None
      
    parentSeqFilter = str(self.ui.cboParentSeqFilter.currentText())
    parentSample1Filter = int(self.ui.spinParentFilterSample1.value())
    parentSample2Filter = int(self.ui.spinParentFilterSample2.value())
    if not self.ui.chkEnableParentSeqFilter.isChecked():
      parentSeqFilter = None
      parentSample1Filter = None
      parentSample2Filter = None

    # effect size filters
    if self.ui.chkEnableEffectSizeFilter1.isChecked():
      effectSizeMeasure1 = self.effectSizeDict[str(self.ui.cboEffectSizeMeasure1.currentText())]    
      minEffectSize1 = float(self.ui.spinMinEffectSize1.value())
    else:
      effectSizeMeasure1 = None
      minEffectSize1 = None
      
    if self.ui.chkEnableEffectSizeFilter2.isChecked():
      effectSizeMeasure2 = self.effectSizeDict[str(self.ui.cboEffectSizeMeasure2.currentText())]    
      minEffectSize2 = float(self.ui.spinMinEffectSize2.value())
    else:
      effectSizeMeasure2 = None
      minEffectSize2 = None
      
    if self.ui.radioOR.isChecked():
      effectSizeOperator = 'OR'
    else:
      effectSizeOperator = 'AND'
               
    self.statsTest.results.filterFeatures(signLevelFilter, seqFilter, sample1Filter, sample2Filter,
                                              parentSeqFilter, parentSample1Filter, parentSample2Filter,
                                              effectSizeMeasure1, minEffectSize1, effectSizeOperator,
                                              effectSizeMeasure2, minEffectSize2)
    
    self.updateFilterInfo()     
    
    # update table summarizing statistical results
    self.populateSummaryTable()
    
    # update plots
    self.statPlots.update(self.statsTest.results)
    
    QtGui.QApplication.instance().restoreOverrideCursor()
       
  def updateFilterInfo(self):
    self.ui.txtNumActiveFeatures.setText(str(len(self.statsTest.results.getActiveFeatures())))
            
  def populateSummaryTable(self):
    tableData = self.statsTest.results.tableData(self.ui.chkShowActiveFeatures.isChecked())
        
    oneMinAlphaStr = str(self.statsTest.results.oneMinusAlpha()*100)
    tableHeadings = list(self.profile.hierarchyHeadings)
    tableHeadings += [str(self.ui.cboSample1.currentText()), str(self.ui.cboSample2.currentText())]
    tableHeadings += ['Parent seq. 1', 'Parent seq. 2']
    tableHeadings += ['Rel. freq. 1 (%)','Rel. freq. 2 (%)']
    tableHeadings += ['p-values','p-values (corrected)']
    tableHeadings += ['Effect size']                     
    tableHeadings += [oneMinAlphaStr + '% lower CI']
    tableHeadings += [oneMinAlphaStr + '% upper CI']
    tableHeadings += ['Power (alpha = ' + str(self.statsTest.results.alpha) + ')']
    tableHeadings += ['Equal sample size (alpha = ' + str(self.statsTest.results.alpha) + '; power = ' + str(self.statsTest.results.oneMinusBeta()) + ')']
    
    self.summaryTable = GenericTable(tableData, tableHeadings, self)
    self.summaryTable.sort(0,QtCore.Qt.AscendingOrder) # start with features in alphabetical order
    self.ui.tableSummary.setModel(self.summaryTable)
    self.ui.tableSummary.verticalHeader().setVisible(False)
    self.ui.tableSummary.resizeColumnsToContents()
    
  def highlightHierarchyExploratoryChanged(self):
    index = self.ui.cboHighlightHierarchyExploratory.currentIndex() - 1
    if index == -1:
      self.preferences['Selected exploratory features'] = []
      self.ui.cboHighlightFeatureExploratory.clear()
      self.exploratoryPlotUpdate()
      return
    
    features = set([])
    for feature in self.profile.profileDict.keys():
      hierarchy = self.profile.getHierarchy(feature)
      features.add(hierarchy[index])
      
    features = list(features)
    features.sort(key=string.lower)
      
    featureStrList = QtCore.QStringList()
    for feature in features:
      featureStrList.append(feature)
      
    self.ui.cboHighlightFeatureExploratory.clear()
    self.ui.cboHighlightFeatureExploratory.insertItems(len(featureStrList),featureStrList)
    self.ui.cboHighlightFeatureExploratory.setCurrentIndex(0)
    
    self.ui.cboHighlightFeatureExploratory.adjustSize()
    
    self.highlightFeatureExploratoryChanged()
    
  def highlightFeatureExploratoryChanged(self):
    QtGui.QApplication.instance().setOverrideCursor(QtGui.QCursor(QtCore.Qt.WaitCursor))   
    
    index = self.ui.cboHighlightHierarchyExploratory.currentIndex() - 1
    selectedFeature = self.ui.cboHighlightFeatureExploratory.currentText()
    
    self.preferences['Selected exploratory features'] = []
    for feature in self.profile.profileDict.keys():
      hierarchy = self.profile.getHierarchy(feature)
      if hierarchy[index] == selectedFeature:
        self.preferences['Selected exploratory features'].append(feature)
  
    self.exploratoryPlotUpdate()
    
    QtGui.QApplication.instance().restoreOverrideCursor()
    
  def highlightHierarchyStatsChanged(self):
    index = self.ui.cboHighlightHierarchyStats.currentIndex() - 1
    if index == -1:
      self.preferences['Selected statistical features'] = []
      self.ui.cboHighlightFeatureStats.clear()
      self.statPlots.update(self.statsTest.results)
      return
    
    features = set([])
    for feature in self.statsTest.results.getActiveFeatures():
      hierarchy = self.statsTest.results.profile.getHierarchy(feature)
      features.add(hierarchy[index])
      
    features = list(features)
    features.sort(key=string.lower)
      
    featureStrList = QtCore.QStringList()
    for feature in features:
      featureStrList.append(feature)
      
    self.ui.cboHighlightFeatureStats.clear()
    self.ui.cboHighlightFeatureStats.insertItems(len(featureStrList),featureStrList)
    self.ui.cboHighlightFeatureStats.setCurrentIndex(0)
    
    self.ui.cboHighlightFeatureStats.adjustSize()
    
    self.highlightFeatureStatsChanged()
  
  def highlightFeatureStatsChanged(self):
    QtGui.QApplication.instance().setOverrideCursor(QtGui.QCursor(QtCore.Qt.WaitCursor))   
    
    index = self.ui.cboHighlightHierarchyStats.currentIndex() - 1
    selectedFeature = self.ui.cboHighlightFeatureStats.currentText()
    
    self.preferences['Selected statistical features'] = []
    for feature in self.statsTest.results.getActiveFeatures():
      hierarchy = self.statsTest.results.profile.getHierarchy(feature)
      if hierarchy[index] == selectedFeature:
        self.preferences['Selected statistical features'].append(feature)
  
    self.statPlots.update(self.statsTest.results)
    
    QtGui.QApplication.instance().restoreOverrideCursor()
          
  def saveImageDlg(self):
    tabWidget = self.ui.tabWidget
    if tabWidget.tabText(tabWidget.currentIndex()) == 'Exploratory plots':
      plotToSave = self.exploratoryPlots
    elif tabWidget.tabText(tabWidget.currentIndex()) == 'Statistical plots':
      plotToSave = self.statPlots
    else:
      QtGui.QMessageBox.information(self, 'Select plot', 'A plot tab must be active to save a plot.', QtGui.QMessageBox.Ok)
      return
     
    file = QtGui.QFileDialog.getSaveFileName(self, 'Save plot...', self.lastDirectory,
                'Portable Network Graphics (*.png);;' +
                'Portable Document Format (*.pdf);;' +
                'PostScript (*.ps);;' +
                'Encapsulated PostScript (*.eps);;' +
                'Scalable Vector Graphics (*.svg)')
    
    if file != '': 
      self.lastDirectory = file[0:file.lastIndexOf('/')]
      try:
        if file[len(file)-3:len(file)] == 'png' or file[len(file)-3:len(file)] == 'PNG':
          dpi, ok = QtGui.QInputDialog.getInteger(self, 'Desired resolution', 'Enter desired resolution (DPI) of image:', 300)
          if ok:
            plotToSave.save(str(file), dpi)
        else:
          plotToSave.save(str(file))
      except IOError:
          QtGui.QMessageBox.information(self, 'Failed to save image', 'Write permission for file denied.', QtGui.QMessageBox.Ok)
           
  def sendPlotToWindow(self): 
    tabWidget = self.ui.tabWidget
    if tabWidget.tabText(tabWidget.currentIndex()) == 'Exploratory plots':
      newWindow = self.exploratoryPlots.sendToNewWindow(self.profile)
    elif tabWidget.tabText(tabWidget.currentIndex()) == 'Statistical plots':
      newWindow = self.statPlots.sendToNewWindow(self.statsTest.results)
    else:
      QtGui.QMessageBox.information(self, 'Send plot to window', 'A plot tab must be active before it can be sent to a new window.', QtGui.QMessageBox.Ok)
      return
    
    self.plotWindows.append(newWindow)

  def saveTableDlg(self):
    tabWidget = self.ui.tabWidget
    if tabWidget.tabText(tabWidget.currentIndex()) == 'Statistical results table':
      tableToSave = self.summaryTable
    elif tabWidget.tabText(tabWidget.currentIndex()) == 'CI coverage':
      tableToSave = self.coverageTable
    elif tabWidget.tabText(tabWidget.currentIndex()) == 'Power test':
      tableToSave = self.powerTable
    else:
      QtGui.QMessageBox.information(self, 'Select table', 'A table tab must be active to save a table.', QtGui.QMessageBox.Ok)
      return
    
    filename = QtGui.QFileDialog.getSaveFileName(self, 'Save table...',self.lastDirectory,
                  'Tab-separated values (*.tsv);;' +
                  'Text file (*.txt);;' +
                  'All files (*.*)')
    if filename != '':
      self.lastDirectory = filename[0:filename.lastIndexOf('/')]
      try:
        tableToSave.save(filename)
      except IOError:
        QtGui.QMessageBox.information(self, 'Failed to save table', 'Write permission for file denied.', QtGui.QMessageBox.Ok)
            
  def confIntervCoverage(self):
    if self.statsTest.results.profile == None:
      return 
        
    confIntervMethod = self.confIntervMethodDict[str(self.ui.cboConfIntervMethods.currentText())]
    coverage = float(self.ui.cboCoverage.currentText()) / 100.0
    trials = int(self.ui.spinConfIntervTrials.value())
    bootstrapRep = int(self.ui.spinConfIntervReplicates.value())
        
    data = self.statsTest.results.contingencyTable(self.ui.chkConfInterActiveFeature.isChecked())
    
    progress = QtGui.QProgressDialog("", QtCore.QString(), 0, len(data)*trials, self)
    progress.setWindowTitle('Estimating CI coverage')
    progress.setWindowModality(QtCore.Qt.WindowModal)
    
    confIntervCoverage = ConfIntervCoverage()
    tableData = confIntervCoverage.run(confIntervMethod, coverage, data, trials, bootstrapRep, progress)
    
    self.populateConfIntervCoverageTable(tableData)
    
  def populateConfIntervCoverageTable(self, tableData):
    confIntervMethodStr = str(self.ui.cboConfIntervMethods.currentText())
    coverageStr = str(self.ui.cboCoverage.currentText()) + '%'
    self.ui.lblCoverageTableTitle.setText('Method = ' + confIntervMethodStr + '; Nominal coverage = ' + coverageStr)
    
    tableHeadings = [str(self.ui.cboProfileLevel.currentText())]
    tableHeadings += [str(self.ui.cboSample1.currentText()), str(self.ui.cboSample2.currentText())]
    tableHeadings += ['Parent seq. 1', 'Parent seq. 2']
    tableHeadings += ['Rel. freq. 1 (%)','Rel. freq. 2 (%)']                  
    tableHeadings += ['Mean coverage', 'Std. dev. coverage']
    tableHeadings += ['Mean coverage (features <= 5 seq.)', 'Std. dev. (features <= 5 seq.)']
    tableHeadings += ['Mean coverage (features > 5 seq.)', 'Std. dev. (features > 5 seq.)']

    self.coverageTable = GenericTable(tableData, tableHeadings, self)

    self.coverageTable.sort(0,QtCore.Qt.AscendingOrder) # start with features in alphabetical order
    self.ui.tableConfInterCoverage.setModel(self.coverageTable)
    self.ui.tableConfInterCoverage.verticalHeader().setVisible(False)
    self.ui.tableConfInterCoverage.resizeColumnsToContents()
    
  def powerTest(self):  
    if self.statsTest.results.profile == None:
      return 
      
    test =  self.statTestDict[str(self.ui.cboStatTests.currentText())]
    signLevel = float(self.ui.cboPowerSignLevel.currentText())   
    trials = int(self.ui.spinPowerTrials.value())
    bootstrapRep = int(self.ui.spinPowerReplicates.value())

    data = self.statsTest.results.contingencyTable(self.ui.chkPowerActiveFeature.isChecked())
    
    progress = QtGui.QProgressDialog("", QtCore.QString(), 0, len(data)*trials, self)
    progress.setWindowTitle('Estimating power')
    progress.setWindowModality(QtCore.Qt.WindowModal)
    
    power = Power()
    tableData = power.run(test, signLevel, data, trials, bootstrapRep, progress)
    
    self.populatePowerTestTable(tableData)
    
  def populatePowerTestTable(self, tableData):
    testStr =  str(self.ui.cboStatTests.currentText())
    signLevel = float(self.ui.cboPowerSignLevel.currentText())  
    self.ui.lblPowerTestTitle.setText('Test = ' + testStr + '; Significance level = ' + str(signLevel))
    
    tableHeadings = [str(self.ui.cboProfileLevel.currentText())]
    tableHeadings += [str(self.ui.cboSample1.currentText()), str(self.ui.cboSample2.currentText())]
    tableHeadings += ['Parent seq. 1', 'Parent seq. 2']
    tableHeadings += ['Rel. freq. 1 (%)','Rel. freq. 2 (%)']                        
    tableHeadings += ['Mean power', 'Std. dev. power']
    tableHeadings += ['Mean power (features <= 5 seq.)', 'Std. dev. (features <= 5 seq.)']
    tableHeadings += ['Mean power (features > 5 seq.)', 'Std. dev. (features > 5 seq.)']

    self.powerTable = GenericTable(tableData, tableHeadings, self)

    self.powerTable.sort(0,QtCore.Qt.AscendingOrder) # start with features in alphabetical order
    self.ui.tablePower.setModel(self.powerTable)
    self.ui.tablePower.verticalHeader().setVisible(False)
    self.ui.tablePower.resizeColumnsToContents()
          
  def openAboutDlg(self):
    QtGui.QMessageBox.about(self, 'About...',
        'STAMP: STatistical Analysis of Metagenomic Profiles\n\n'
        '%s\n'
        '%s\n'
        '%s\n\n' 
        '%s' % ('Donovan Parks and Robert Beiko', 'v1.08', 'March 13, 2011', 'Program icon by Caihua (http://commons.wikimedia.org/wiki/File:Fairytale_colors.png)'))

def exceptHook(exc_type, exc_value, exc_traceback):
  ## Copyright (c) 2002-2007 Pascal Varet <p.varet@gmail.com>
  ##
  ## Originally part of Spyrit.
  
  import traceback

  ## KeyboardInterrupt is a special case.
  ## We don't raise the error dialog when it occurs.
  if issubclass( exc_type, KeyboardInterrupt ):
    if qApp():
      qApp().quit()
    return

  filename, line, dummy, dummy = traceback.extract_tb( exc_traceback ).pop()
  filename = os.path.basename( filename )
  error    = "%s: %s" % ( exc_type.__name__, exc_value )

  QtGui.QMessageBox.critical(mainWindow, "Unknown error...",
    "<center>An error has occured. This is most likely a bug in STAMP. The error is:<br/><br/>"
  + "<b><i>%s</i></b><br/>" % error
  + "It occured at <b>line %d</b> of file <b>%s</b>.<br/>" % ( line, filename )
  + "</center>" )
		
if __name__ == "__main__":     
  # change the current working directory
  os.chdir(getMainDir())
  
  # initialize preferences
  preferences = {}
  preferences['Pseudocount'] = 0.5
  preferences['Truncate feature names'] = True
  preferences['Length of truncated feature names'] = 50
  preferences['Sample 1 colour'] = QtGui.QColor(0,0,255)
  preferences['Sample 2 colour'] = QtGui.QColor(255,127,0)
  preferences['Selected exploratory features'] = []
  preferences['Selected statistical features'] = []
  preferences['Executable directory'] = sys.path[0]
  
  if len(sys.argv) == 2 and sys.argv[1].startswith("--macapp"):
    del sys.argv[1]
	
  if len(sys.argv) == 1:
    sys.excepthook = exceptHook
    app = QtGui.QApplication(sys.argv)
    mainWindow = MainWindow(preferences)
    mainWindow.show()
    sys.exit(app.exec_())
	  
  else:
    commandLineParser = CommandLineParser(preferences)
    commandLineParser.run()
    sys.exit()
