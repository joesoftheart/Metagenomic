'''
Bar plot.

@author: Donovan Parks
'''

import sys

from PyQt4 import QtGui, QtCore
import numpy as np

from matplotlib.ticker import ScalarFormatter


from plugins.AbstractStatPlotPlugin import AbstractStatPlotPlugin, TestWindow, ConfigureDialog
from plugins.statPlots.configGUI.barUI import Ui_BarConfigDialog
from metagenomics import TableHelper

class Bar(AbstractStatPlotPlugin):
  '''
  Bar plot.
  '''
  def __init__(self, preferences, parent=None):
    AbstractStatPlotPlugin.__init__(self, preferences, parent)
    self.preferences = preferences
   
    self.name = 'Bar plot'
    
    self.figWidth = 8.5
    self.figHeightPerRow = 0.20
    
    self.sampleName1 = ''
    self.sampleName2 = ''
    self.fieldToPlot = 'Number of sequences'
    self.legendPos = 0  # best position
    self.bSortFeatures = True

    self.xLabel = 'Number of sequences'
    
  def mirrorProperties(self, plotToCopy):
    self.name = plotToCopy.name
    self.figWidth = plotToCopy.figWidth
    self.figHeightPerRow = plotToCopy.figHeightPerRow
    self.sampleName1 = plotToCopy.sampleName1
    self.sampleName2 = plotToCopy.sampleName2
    self.fieldToPlot = plotToCopy.fieldToPlot
    self.legendPos = plotToCopy.legendPos
    self.bSortFeatures = plotToCopy.bSortFeatures
    self.xLabel = plotToCopy.xLabel
    
  def plot(self, statsResults):    
    if len(statsResults.activeData) <= 0:
      self.emptyAxis()      
      return
    
    features = statsResults.getColumn('Features')
    if len(features) > 200:
      QtGui.QApplication.instance().setOverrideCursor(QtGui.QCursor(QtCore.Qt.ArrowCursor))
      reply = QtGui.QMessageBox.question(self, 'Continue?', 'Profile contains ' + str(len(features)) + ' features. ' +
                                    'It may take several seconds to generate this plot. We recommend filtering your profile first. ' + 
                                    'Do you wish to continue?', QtGui.QMessageBox.Yes, QtGui.QMessageBox.No)
      QtGui.QApplication.instance().restoreOverrideCursor()
      if reply == QtGui.QMessageBox.No:
        self.emptyAxis()  
        return

    # *** Colour of plot elements
    profile1Colour = str(self.preferences['Sample 1 colour'].name())
    profile2Colour = str(self.preferences['Sample 2 colour'].name())
    
    # *** Set sample names
    bLogScale = False
    xLabel = self.fieldToPlot
    if self.sampleName1 == '' and self.sampleName2 == '':
      self.sampleName1 = statsResults.profile.sampleNames[0]
      self.sampleName2 = statsResults.profile.sampleNames[1]
    
    # *** Create lists for each quantity of interest
    if self.fieldToPlot == 'Number of sequences':
      if self.bSortFeatures:
        statsResults.activeData = TableHelper.SortTable(statsResults.activeData,\
                                                        [statsResults.dataHeadings['Seq1']], True)
      field1 = statsResults.getColumn('Seq1')
      field2 = statsResults.getColumn('Seq2')
      

    elif self.fieldToPlot == 'Number of parental sequences':
      if self.bSortFeatures:
        statsResults.activeData = TableHelper.SortTable(statsResults.activeData,\
                                                        [statsResults.dataHeadings['ParentalSeq1']], True)
      field1 = statsResults.getColumn('ParentalSeq1')
      field2 = statsResults.getColumn('ParentalSeq2')
      
    elif self.fieldToPlot == 'Relative frequency':
      if self.bSortFeatures:
        statsResults.activeData = TableHelper.SortTable(statsResults.activeData,\
                                                        [statsResults.dataHeadings['RelFreq1']], True)
      field1 = statsResults.getColumn('RelFreq1')
      field2 = statsResults.getColumn('RelFreq2')
      
    elif self.fieldToPlot == 'p-values':
      if self.bSortFeatures:
        statsResults.activeData = TableHelper.SortTable(statsResults.activeData,\
                                                        [statsResults.dataHeadings['pValues']], False)
      field1 = statsResults.getColumn('pValues')
      field2 = None
      
    elif self.fieldToPlot == 'p-values (corrected)':
      if self.bSortFeatures:
        statsResults.activeData = TableHelper.SortTable(statsResults.activeData,\
                                                        [statsResults.dataHeadings['pValuesCorrected']], False)
      field1 = statsResults.getColumn('pValuesCorrected')
      field2 = None
            
    elif self.fieldToPlot == 'Effect size':
      if self.bSortFeatures:
        statsResults.activeData = TableHelper.SortTable(statsResults.activeData,\
                                                        [statsResults.dataHeadings['EffectSize']], True, True,
                                                        statsResults.confIntervMethod.bRatio)
      field1 = statsResults.getColumn('EffectSize')
      field2 = None
      
      bLogScale = statsResults.confIntervMethod.bRatio
      xLabel = statsResults.confIntervMethod.plotLabel

    # *** Truncate feature labels
    selectedFeatures = list(self.preferences['Selected statistical features'])
    if self.preferences['Truncate feature names']:
      length = self.preferences['Length of truncated feature names']
      
      for i in xrange(0, len(features)):
        if len(features[i]) > length+3:
          features[i] = features[i][0:length] + '...'

      for i in xrange(0, len(selectedFeatures)):
        if len(selectedFeatures[i]) > length+3:
          selectedFeatures[i] = selectedFeatures[i][0:length] + '...'
            
    # *** Check that there is at least one significant feature
    if len(features) <= 0:
      self.emptyAxis('No significant features')      
      return

    # *** Set figure size
    padding = 0.2               #inches
    heightBottomLabels = 0.4    # inches
    
    imageHeight = len(features)*self.figHeightPerRow + padding + heightBottomLabels
    self.fig.set_size_inches(self.figWidth, imageHeight)  
              
    yPlotOffsetFigSpace = heightBottomLabels / imageHeight 
    heightPlotFigSpace = 1.0 - yPlotOffsetFigSpace - padding / imageHeight
       
    yLabelBounds = self.yLabelExtents(features, 8)
    xPlotOffsetFigSpace = yLabelBounds.width + 0.1 / self.figWidth
    widthPlotFigSpace = 1.0 - xPlotOffsetFigSpace - padding / self.figWidth
    
    axesBar = self.fig.add_axes([xPlotOffsetFigSpace,yPlotOffsetFigSpace,widthPlotFigSpace,heightPlotFigSpace])
    
    # *** Plot data
    barHeight = 0.35 
    
    if bLogScale:
      field1 = np.log10(field1)
      xLabel = 'log(' + xLabel + ')'
      if field2 != None:
        field2 = np.log10(field2)
    
    if field2 == None:
      rects1 = axesBar.barh(np.arange(len(features)), field1, height=barHeight)  
      axesBar.set_yticks(np.arange(len(features)) + 0.5*barHeight)    
      axesBar.set_ylim([0, len(features)-1.0 + barHeight + 0.1])      
    elif field2 != None:
      rects2 = axesBar.barh(np.arange(len(features)), field2, height=barHeight, color=profile2Colour)  
      rects1 = axesBar.barh(np.arange(len(features))+barHeight, field1, height=barHeight, color=profile1Colour)
      axesBar.set_yticks(np.arange(len(features)) + barHeight)    
      axesBar.set_ylim([0, len(features)-1.0 + 2*barHeight + 0.1])       
        
    axesBar.set_yticklabels(features, size=8)  
    axesBar.set_xlabel(xLabel, size=8)
    
    scalarFormatter = ScalarFormatter(useMathText=False)
    scalarFormatter.set_scientific(True)
    scalarFormatter.set_powerlimits((-3,4))
    axesBar.xaxis.set_major_formatter(scalarFormatter)

    # *** Prettify plot
    if field2 != None:
      legend = axesBar.legend([rects1[0], rects2[0]], (self.sampleName1, self.sampleName2), loc=self.legendPos)
      legend.get_frame().set_linewidth(0)
      
      for label in legend.get_texts():
        label.set_size(8)
    
    for label in axesBar.get_xticklabels():
      label.set_size(8)
        
    for label in axesBar.get_yticklabels():
      label.set_size(8)
      if label.get_text() in selectedFeatures:
          label.set_color('red')
      
    for a in axesBar.yaxis.majorTicks:
      a.tick1On=False
      a.tick2On=False
        
    for a in axesBar.xaxis.majorTicks:
      a.tick1On=True
      a.tick2On=False
      
    for loc, spine in axesBar.spines.iteritems():
      if loc in ['right','top']:
          spine.set_color('none') 
    
    self.updateGeometry()       
    self.draw()

  def configure(self, statsResults):
    self.statsResults = statsResults
    
    configDlg = ConfigureDialog(Ui_BarConfigDialog)
    
    configDlg.ui.cboFieldToPlot.clear()
    configDlg.ui.cboFieldToPlot.addItem('Effect size')
    configDlg.ui.cboFieldToPlot.addItem('Number of sequences')
    configDlg.ui.cboFieldToPlot.addItem('Number of parental sequences')
    configDlg.ui.cboFieldToPlot.addItem('p-values')
    configDlg.ui.cboFieldToPlot.addItem('p-values (corrected)')
    configDlg.ui.cboFieldToPlot.addItem('Relative frequency')
    
    configDlg.ui.cboFieldToPlot.setCurrentIndex(configDlg.ui.cboFieldToPlot.findText(self.fieldToPlot))
    
    configDlg.ui.chkSort.setChecked(self.bSortFeatures)

    configDlg.ui.txtSampleName1.setText(self.sampleName1)
    configDlg.ui.txtSampleName2.setText(self.sampleName2)
    
    configDlg.ui.spinFigWidth.setValue(self.figWidth)
    configDlg.ui.spinFigRowHeight.setValue(self.figHeightPerRow)
    
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
      self.bSortFeatures = configDlg.ui.chkSort.isChecked()
      self.sampleName1 = str(configDlg.ui.txtSampleName1.text())
      self.sampleName2 = str(configDlg.ui.txtSampleName2.text())
      self.figWidth = configDlg.ui.spinFigWidth.value()
      self.figHeightPerRow = configDlg.ui.spinFigRowHeight.value()
      
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

      self.plot(statsResults)
          
if __name__ == "__main__": 
  app = QtGui.QApplication(sys.argv)
  testWindow = TestWindow(Bar)
  testWindow.show()
  sys.exit(app.exec_())


        