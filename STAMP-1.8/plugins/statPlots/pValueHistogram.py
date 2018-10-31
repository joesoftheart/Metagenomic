'''
p-value histogram plot.

@author: Donovan Parks
'''

import sys

from PyQt4 import QtGui, QtCore

from mpl_toolkits.axes_grid.inset_locator import inset_axes

from plugins.AbstractStatPlotPlugin import AbstractStatPlotPlugin, TestWindow, ConfigureDialog
from plugins.statPlots.configGUI.pValueHistogramUI import Ui_pValueHistogramDialog

class pValueHistogram(AbstractStatPlotPlugin):
  '''
  p-value histogram plot.
  '''
  def __init__(self, preferences, parent=None):
    AbstractStatPlotPlugin.__init__(self, preferences, parent)
    self.preferences = preferences
    
    self.name = 'p-value histogram'
    self.figWidth = 6.0
    self.figHeight = 6.0
    self.binWidth = 0.01
    self.yAxisLogScale = False
    self.fieldToPlot = 'p-values (corrected)'
    
    self.bShowInset = True
    self.insetWidth = 60
    self.insetHeight = 60
    self.insetBinWidth = 0.002
    self.xLimit = 0.05
    self.insetLogScale = False

  def mirrorProperties(self, plotToCopy):
    self.name = plotToCopy.name
    self.figWidth = plotToCopy.figWidth
    self.figHeight = plotToCopy.figHeight
    self.yAxisLogScale = plotToCopy.yAxisLogScale
    self.fieldToPlot = plotToCopy.fieldToPlot
    
    self.bShowInset = plotToCopy.bShowInset
    self.insetWidth = plotToCopy.insetWidth
    self.insetHeight = plotToCopy.insetHeight
    self.insetBinWidth = plotToCopy.insetBinWidth
    self.xLimit = plotToCopy.xLimit
    self.insetLogScale = plotToCopy.insetLogScale
    
  def plot(self, statsResults):
    if len(statsResults.activeData) <= 0:
      self.emptyAxis()      
      return
    
    # *** Get data to plot 
    if self.fieldToPlot == 'p-values':
      data = statsResults.getColumn('pValues')
      xLabel = 'p-value'
    elif self.fieldToPlot == 'p-values (corrected)':
      data = statsResults.getColumn('pValuesCorrected')
      xLabel = 'p-value (corrected)'
    
    # *** Set size of figure
    self.fig.clear()
    self.fig.set_size_inches(self.figWidth, self.figHeight) 
    heightBottomLabels = 0.4  # inches
    widthSideLabel = 0.5      # inches 
    padding = 0.2             # inches
    axesHist = self.fig.add_axes([widthSideLabel/self.figWidth,heightBottomLabels/self.figHeight,\
                                    1.0-(widthSideLabel+padding)/self.figWidth,\
                                    1.0-(heightBottomLabels+padding)/self.figHeight])
    
    # *** Histogram plot 
    bins = [0.0]
    binWidth = self.binWidth
    binEnd = binWidth
    while binEnd <= 1.0:
      bins.append(binEnd)
      binEnd += binWidth
      
    n, bins, patch = axesHist.hist(data, bins=bins, log=self.yAxisLogScale)      
    axesHist.set_xlabel(xLabel, size=8)
    axesHist.set_ylabel('Number of features', size=8)
    
    # *** Prettify plot   
    for label in axesHist.get_xticklabels():
      label.set_size(8)
        
    for label in axesHist.get_yticklabels():
      label.set_size(8)
      
    for a in axesHist.yaxis.majorTicks:
      a.tick1On=True
      a.tick2On=False
        
    for a in axesHist.xaxis.majorTicks:
      a.tick1On=True
      a.tick2On=False
      
    for loc, spine in axesHist.spines.iteritems():
      if loc in ['right','top']:
          spine.set_color('none') 
    
    # *** Plot inset    
    if self.bShowInset:
      bins = [0.0]
      binWidth = self.insetBinWidth
      binEnd = binWidth
      while binEnd <= self.xLimit:
        bins.append(binEnd)
        binEnd += binWidth
        
      widthStr = str(self.insetWidth) + '%'
      heightStr = str(self.insetHeight) + '%'
      axins = inset_axes(axesHist, width=widthStr, height=heightStr, loc=1)
      filteredData = [d for d in data if d <= self.xLimit]
      n, bins, patch = axins.hist(filteredData, bins=bins, log=self.insetLogScale)  
      axins.set_xlim(0, self.xLimit)
      
      # *** Prettify inset   
      for label in axins.get_xticklabels():
        label.set_size(8)
          
      for label in axins.get_yticklabels():
        label.set_size(8)
        
      for a in axins.yaxis.majorTicks:
        a.tick1On=True
        a.tick2On=False
          
      for a in axins.xaxis.majorTicks:
        a.tick1On=True
        a.tick2On=False
        
      for loc, spine in axins.spines.iteritems():
        if loc in ['right','top']:
            spine.set_color('none') 
        
    self.updateGeometry()       
    self.draw()
  
  def configure(self, statsResults):
    self.statsResults = statsResults
    
    self.configDlg = ConfigureDialog(Ui_pValueHistogramDialog)
    
    self.connect(self.configDlg.ui.btnXmax, QtCore.SIGNAL('clicked()'), self.setXaxisMax)
    
    self.configDlg.ui.cboFieldToPlot.setCurrentIndex(self.configDlg.ui.cboFieldToPlot.findText(self.fieldToPlot))
    
    self.configDlg.ui.spinFigWidth.setValue(self.figWidth)
    self.configDlg.ui.spinFigHeight.setValue(self.figHeight)
    
    self.configDlg.ui.spinBinWidth.setValue(self.binWidth)     
    self.configDlg.ui.chkLogScale.setChecked(self.yAxisLogScale)
    
    self.configDlg.ui.chkShowInset.setChecked(self.bShowInset)
    self.configDlg.ui.spinInsetWidth.setValue(self.insetWidth)
    self.configDlg.ui.spinInsetHeight.setValue(self.insetHeight)
    self.configDlg.ui.spinInsetBinWidth.setValue(self.insetBinWidth)
    self.configDlg.ui.spinXlimit.setValue(self.xLimit)
    self.configDlg.ui.chkInsetLogScale.setChecked(self.insetLogScale)
    
    if self.configDlg.exec_() == QtGui.QDialog.Accepted:     
      self.figWidth = self.configDlg.ui.spinFigWidth.value()
      self.figHeight = self.configDlg.ui.spinFigHeight.value()

      self.binWidth = self.configDlg.ui.spinBinWidth.value()
      self.yAxisLogScale = self.configDlg.ui.chkLogScale.isChecked()
      
      self.fieldToPlot = self.configDlg.ui.cboFieldToPlot.currentText()
      
      self.bShowInset = self.configDlg.ui.chkShowInset.isChecked()
      self.insetWidth = self.configDlg.ui.spinInsetWidth.value()
      self.insetHeight = self.configDlg.ui.spinInsetHeight.value()
      self.insetBinWidth = self.configDlg.ui.spinInsetBinWidth.value()
      self.xLimit = self.configDlg.ui.spinXlimit.value()
      self.insetLogScale = self.configDlg.ui.chkInsetLogScale.isChecked()
      
      self.plot(statsResults)
      
  def setXaxisMax(self):
    # *** Get data to plot 
    if self.configDlg.ui.cboFieldToPlot.currentText() == 'p-values':
      data = self.statsResults.getColumn('pValues')
    else:
      data = self.statsResults.getColumn('pValuesCorrected')
      
    self.configDlg.ui.spinXlimit.setValue(max(data))

if __name__ == "__main__": 
  app = QtGui.QApplication(sys.argv)
  testWindow = TestWindow(pValueHistogram)
  testWindow.show()
  sys.exit(app.exec_())


        