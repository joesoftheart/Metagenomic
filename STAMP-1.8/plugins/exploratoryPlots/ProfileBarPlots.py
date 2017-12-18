'''
Plot exploratory data for different features.

@author: Donovan Parks
'''

import sys

from PyQt4 import QtGui, QtCore
import numpy as np

from plugins.AbstractExploratoryPlotPlugin import AbstractExploratoryPlotPlugin, TestWindow, ConfigureDialog
from plugins.exploratoryPlots.configGUI.profileBarPlotUI import Ui_ProfileBarPlotDialog

from metagenomics.stats.distributions.NormalDist import inverseNormalCDF
from metagenomics.stats.CI.WilsonCI import WilsonCI

class ProfileBarPlots(AbstractExploratoryPlotPlugin):
  '''
  Profile bar plots.
  '''
  def __init__(self, preferences, parent=None):
    AbstractExploratoryPlotPlugin.__init__(self, preferences, parent)
    self.preferences = preferences
   
    self.name = 'Profile bar plot'
    self.figColWidth = 0.25
    self.figHeight = 6.0
    
    self.sampleName1 = ''
    self.sampleName2 = ''
    self.fieldToPlot = 'Proportion of sequences'
    self.legendPos = 0  # best position
    
    self.bShowCIs = True
    self.endCapSize = 0


  def mirrorProperties(self, plotToCopy):
    self.name = plotToCopy.name
    self.figColWidth = plotToCopy.figColWidth
    self.figHeight = plotToCopy.figHeight
    self.sampleName1 = plotToCopy.sampleName1
    self.sampleName2 = plotToCopy.sampleName2
    self.fieldToPlot = plotToCopy.fieldToPlot
    self.legendPos = plotToCopy.legendPos
    self.bShowCIs = plotToCopy.bShowCIs
    self.endCapSize = plotToCopy.endCapSize
    
  def plot(self, profile):
    if len(profile.profileDict) <= 0:
      self.emptyAxis()      
      return
    
    if len(profile.profileDict) > 200:
      QtGui.QApplication.instance().setOverrideCursor(QtGui.QCursor(QtCore.Qt.ArrowCursor))
      reply = QtGui.QMessageBox.question(self, 'Continue?', 'Profile contains ' + str(len(profile.profileDict)) + ' features. ' +
                                    'It may take several seconds to generate this plot. Exploring the data at a higher hierarchy level is recommended. ' + 
                                    'Do you wish to continue?', QtGui.QMessageBox.Yes, QtGui.QMessageBox.No)
      QtGui.QApplication.instance().restoreOverrideCursor()
      if reply == QtGui.QMessageBox.No:
        self.emptyAxis()  
        return
        
    # *** Colour of plot elements
    profile1Colour = str(self.preferences['Sample 1 colour'].name())
    profile2Colour = str(self.preferences['Sample 2 colour'].name())
    
    # *** Set sample names
    if self.sampleName1 == '' and self.sampleName2 == '':
      self.sampleName1 = profile.sampleNames[0]
      self.sampleName2 = profile.sampleNames[1]
            
    # *** Create lists for each quantity of interest and calculate CIs
    wilsonCI = WilsonCI()
    zCoverage = inverseNormalCDF(0.95)
    confInter1 = []
    confInter2 = []
    
    tables = profile.getLabeledTables()
    features = []
    field1 = []
    field2 = []
    if self.fieldToPlot == 'Number of sequences':
      for table in tables:
        feature, seq1, seq2, parentSeq1, parentSeq2 = table
        features.append(feature)
        field1.append(seq1)
        field2.append(seq2)
        
        if self.bShowCIs:
          lowerCI, upperCI, p = wilsonCI.run(seq1, parentSeq1, 0.95, zCoverage)
          confInter1.append(max((p - lowerCI)*parentSeq1, 0))
          
          lowerCI, upperCI, p = wilsonCI.run(seq2, parentSeq2, 0.95, zCoverage)
          confInter2.append(max((p - lowerCI)*parentSeq2, 0))
        else:
          confInter1.append(0)
          confInter2.append(0)
        
    elif self.fieldToPlot == 'Proportion of sequences':
      for table in tables:
        feature, seq1, seq2, parentSeq1, parentSeq2 = table
        features.append(feature)
        field1.append(float(seq1)*100 / max(parentSeq1,1))
        field2.append(float(seq2)*100 / max(parentSeq2,1))
        
        if self.bShowCIs:
          lowerCI, upperCI, p = wilsonCI.run(seq1, parentSeq1, 0.95, zCoverage)
          confInter1.append(max((p - lowerCI)*100,0))
          
          lowerCI, upperCI, p = wilsonCI.run(seq2, parentSeq2, 0.95, zCoverage)
          confInter2.append(max((p - lowerCI)*100,0))
        else:
          confInter1.append(0)
          confInter2.append(0)
        
    # *** Sort fields so they are in descending order of the values in sample 1
    fields = zip(field1, field2, features, confInter1, confInter2)
    fields.sort(reverse = True)
    field1, field2, features, confInter1, confInter2 = zip(*fields)
    features = list(features)
        
    # *** Truncate feature labels
    selectedFeatures = list(self.preferences['Selected exploratory features'])
    if self.preferences['Truncate feature names']:
      length = self.preferences['Length of truncated feature names']
            
      for i in xrange(0, len(features)):
        if len(features[i]) > length+3:
          features[i] = features[i][0:length] + '...'
          
      for i in xrange(0, len(selectedFeatures)):
        if len(selectedFeatures[i]) > length+3:
          selectedFeatures[i] = selectedFeatures[i][0:length] + '...'
          
    # *** Set figure size
    self.fig.clear()
    figWidth = self.figColWidth*len(features)
    figHeight = self.figHeight
    if figWidth > 256 or figHeight > 256:
        QtGui.QApplication.instance().setOverrideCursor(QtGui.QCursor(QtCore.Qt.ArrowCursor))
        self.emptyAxis()  
        QtGui.QMessageBox.question(self, 'Excessively large plot', 'The resulting plot is too large to display.')
        QtGui.QApplication.instance().restoreOverrideCursor()
        return

    self.fig.set_size_inches(figWidth, figHeight)  
    xLabelBounds, yLabelBounds = self.labelExtents(features, 8, 90, [max(max(field1), max(field2))], 8, 0)
    
    padding = 0.1           # inches
    newFigWidth = figWidth * (1.0+yLabelBounds.width) + 2*padding
    self.fig.set_size_inches(figWidth * (1.0+yLabelBounds.width) + 2*padding, figHeight)  

    xOffsetFigSpace = (yLabelBounds.width*figWidth)/newFigWidth + padding/newFigWidth
    yOffsetFigSpace = xLabelBounds.height + padding/figHeight
    axesBar = self.fig.add_axes([xOffsetFigSpace, yOffsetFigSpace,
                                    1.0 - xOffsetFigSpace - padding/newFigWidth, 1.0 - yOffsetFigSpace - padding/figHeight])

    # *** Plot data
    colWidth = self.figColWidth
    barWidth = (colWidth*0.9) / 2
    
    if self.bShowCIs == True:  
      rects1 = axesBar.bar(np.arange(len(features))*colWidth, field1, width=barWidth, color=profile1Colour, yerr=confInter1, ecolor='black', capsize=self.endCapSize)  
      rects2 = axesBar.bar(np.arange(len(features))*colWidth + barWidth, field2, width=barWidth, color=profile2Colour, yerr=confInter2, ecolor='black', capsize=self.endCapSize)
    else:
      rects1 = axesBar.bar(np.arange(len(features))*colWidth, field1, width=barWidth, color=profile1Colour)  
      rects2 = axesBar.bar(np.arange(len(features))*colWidth + barWidth, field2, width=barWidth, color=profile2Colour)
    
    axesBar.set_xticks(np.arange(len(features))*colWidth + barWidth)    
    axesBar.set_xlim([0, (len(features)-1.0)*colWidth + 2*barWidth + 0.1])       
    axesBar.set_xticklabels(features, size=8)  

    # *** Prettify plot
    legend = axesBar.legend([rects1[0], rects2[0]], (self.sampleName1, self.sampleName2), loc=self.legendPos)
    legend.get_frame().set_linewidth(0)
      
    for label in legend.get_texts():
      label.set_size(8)
    
    for label in axesBar.get_xticklabels():
      label.set_size(8)
      label.set_rotation(90)
      if label.get_text() in selectedFeatures:
          label.set_color('red')
        
    for label in axesBar.get_yticklabels():
      label.set_size(8)
      
    for a in axesBar.yaxis.majorTicks:
      a.tick1On=False
      a.tick2On=False
        
    for a in axesBar.xaxis.majorTicks:
      a.tick1On=False
      a.tick2On=False
      
    for loc, spine in axesBar.spines.iteritems():
      if loc in ['right','top']:
          spine.set_color('none') 

    self.updateGeometry()       
    self.draw()

  def configure(self, profile):
    configDlg = ConfigureDialog(Ui_ProfileBarPlotDialog)
    
    configDlg.ui.cboFieldToPlot.setCurrentIndex(configDlg.ui.cboFieldToPlot.findText(self.fieldToPlot))
    
    configDlg.ui.txtSampleName1.setText(self.sampleName1)
    configDlg.ui.txtSampleName2.setText(self.sampleName2)
        
    configDlg.ui.spinFigColWidth.setValue(self.figColWidth)
    configDlg.ui.spinFigHeight.setValue(self.figHeight)
    
    configDlg.ui.chkShowCIs.setChecked(self.bShowCIs)
    configDlg.ui.spinEndCapSize.setValue(self.endCapSize)
    
    # legend position
    if self.legendPos == 0:
      configDlg.ui.radioLegendPosBest.setDown(True)
    elif self.legendPos == 1:
      configDlg.ui.radioLegendPosUpperRight.setChecked(True)
    elif self.legendPos == 7:
      configDlg.ui.radioLegendPosCentreRight.setChecked(True)
    elif self.legendPos == 4:
      configDlg.ui.radioLegendPosLowerRight.setChecked(True)
    elif self.legendPos == 2:
      configDlg.ui.radioLegendPosUpperLeft.setChecked(True)
    elif self.legendPos == 6:
      configDlg.ui.radioLegendPosCentreLeft.setChecked(True)
    elif self.legendPos == 3:
      configDlg.ui.radioLegendPosLowerLeft.setChecked(True)
    
    if configDlg.exec_() == QtGui.QDialog.Accepted:   
      self.fieldToPlot = str(configDlg.ui.cboFieldToPlot.currentText())
        
      self.sampleName1 = str(configDlg.ui.txtSampleName1.text())
      self.sampleName2 = str(configDlg.ui.txtSampleName2.text())
      
      self.figColWidth = configDlg.ui.spinFigColWidth.value()
      self.figHeight = configDlg.ui.spinFigHeight.value()
      
      self.bShowCIs = configDlg.ui.chkShowCIs.isChecked()
      self.endCapSize = configDlg.ui.spinEndCapSize.value()
      
      # legend position      
      if configDlg.ui.radioLegendPosBest.isChecked() == True:
        self.legendPos = 0
      elif configDlg.ui.radioLegendPosUpperRight.isChecked() == True:
        self.legendPos = 1
      elif configDlg.ui.radioLegendPosCentreRight.isChecked() == True:
        self.legendPos = 7
      elif configDlg.ui.radioLegendPosLowerRight.isChecked() == True:
        self.legendPos = 4
      elif configDlg.ui.radioLegendPosUpperLeft.isChecked() == True:
        self.legendPos = 2
      elif configDlg.ui.radioLegendPosCentreLeft.isChecked() == True:
        self.legendPos = 6
      elif configDlg.ui.radioLegendPosLowerLeft.isChecked() == True:
        self.legendPos = 3
      
      self.plot(profile)
          
if __name__ == "__main__": 
  app = QtGui.QApplication(sys.argv)
  testWindow = TestWindow(ProfileBarPlots)
  testWindow.show()
  sys.exit(app.exec_())


        