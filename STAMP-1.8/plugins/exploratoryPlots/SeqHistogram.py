'''
Sequence histogram plot.

@author: Donovan Parks
'''

import sys
import math

from PyQt4 import QtCore, QtGui

from plugins.AbstractExploratoryPlotPlugin import AbstractExploratoryPlotPlugin, TestWindow, ConfigureDialog
from plugins.exploratoryPlots.configGUI.seqHistogramUI import Ui_SeqHistogramDialog

class SeqHistogram(AbstractExploratoryPlotPlugin):
  '''
  Sequence histogram plot.
  '''
  def __init__(self, preferences, parent=None):
    AbstractExploratoryPlotPlugin.__init__(self, preferences, parent)
    self.preferences = preferences
    
    self.name = 'Sequence histogram'
    self.figWidth = 6.0
    self.figHeight = 6.0
    self.fieldToPlot = 'Sequences'
    self.sampleName1 = None
    self.sampleName2 = None

    self.bCustomBinWidth = False
    self.binWidth = 1
    self.yAxisLogScale = False
    
    self.bCustomXaxis = False
    self.xLimitLeft = None
    self.xLimitRight = None

    self.xLabel = 'Sequences'
    
  def mirrorProperties(self, plotToCopy):
    self.name = plotToCopy.name
    self.figWidth = plotToCopy.figWidth
    self.figHeight = plotToCopy.figHeight
    self.fieldToPlot = plotToCopy.fieldToPlot
    self.sampleName1 = plotToCopy.sampleName1
    self.sampleName2 = plotToCopy.sampleName2

    self.bCustomBinWidth = plotToCopy.bCustomBinWidth
    self.binWidth = plotToCopy.binWidth
    self.yAxisLogScale = plotToCopy.yAxisLogScale
    
    self.bCustomXaxis = plotToCopy.bCustomXaxis
    self.xLimitLeft = plotToCopy.xLimitLeft
    self.xLimitRight = plotToCopy.xLimitRight

    self.xLabel = plotToCopy.xLabel
    
  def plot(self, profile):
    if len(profile.profileDict) <= 0:
      self.emptyAxis()      
      return
    
    # *** Colour of plot elements
    profile1Colour = str(self.preferences['Sample 1 colour'].name())
    profile2Colour = str(self.preferences['Sample 2 colour'].name()) 
       
    # *** Set sample names
    if self.sampleName1 == None and self.sampleName2 == None:
      self.sampleName1 = profile.sampleNames[0]
      self.sampleName2 = profile.sampleNames[1]
    
    # *** Get sequence counts
    if self.fieldToPlot == 'Sequences':    
      seqs1 = profile.getSequenceCounts(0)
      seqs2 = profile.getSequenceCounts(1)
    elif self.fieldToPlot == 'Parental sequences':
      seqs1 = profile.getSequenceCounts(0, True)
      seqs2 = profile.getSequenceCounts(1, True)
    
    # *** Set x-axis limit
    self.xMin = min(min(seqs1),min(seqs2))
    if self.xLimitLeft == None:
      self.xLimitLeft = self.xMin
      
    self.xMax = max(max(seqs1),max(seqs2))
    if self.xLimitRight == None:
      self.xLimitRight = self.xMax   
      
    # Set bin width
    if not self.bCustomBinWidth:
      self.binWidth = (self.xMax - self.xMin) / 50.0
    
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
    binStart = math.floor( float(self.xMin) / self.binWidth ) * self.binWidth
    bins = [binStart]
    binEnd = binStart + self.binWidth
    while binEnd <= self.xMax:
      bins.append(binEnd)
      binEnd += self.binWidth
    bins.append(binEnd)
 
    n, bins, patches = axesHist.hist(zip(seqs1, seqs2), bins=bins, log=self.yAxisLogScale)  
    for patch in patches[0]:
      patch.set_facecolor(profile1Colour)
    for patch in patches[1]:
      patch.set_facecolor(profile2Colour)
      
    if self.bCustomXaxis:
      axesHist.set_xlim(self.xLimitLeft, self.xLimitRight)
      
    axesHist.set_xlabel(self.xLabel, size=8)
    axesHist.set_ylabel('Number of features', size=8)
    
    # *** Prettify plot
    legend = axesHist.legend([patches[0][0], patches[1][0]], (self.sampleName1, self.sampleName2), loc=0)
    legend.get_frame().set_linewidth(0)
      
    for label in legend.get_texts():
      label.set_size(8)
    
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
    
    self.updateGeometry()       
    self.draw()
  
  def configure(self, profile):  
    self.profile = profile
    
    self.configDlg = ConfigureDialog(Ui_SeqHistogramDialog)
    
    self.connect(self.configDlg.ui.chkCustomBinWidth, QtCore.SIGNAL('toggled(bool)'), self.changeCustomBinWidth)
    self.connect(self.configDlg.ui.chkCustomXaxis, QtCore.SIGNAL('toggled(bool)'), self.changeCustomXaxis)
    self.connect(self.configDlg.ui.btnXmin, QtCore.SIGNAL('clicked()'), self.setXaxisMin)
    self.connect(self.configDlg.ui.btnXmax, QtCore.SIGNAL('clicked()'), self.setXaxisMax)
 
    self.configDlg.ui.cboFieldToPlot.setCurrentIndex(self.configDlg.ui.cboFieldToPlot.findText(self.fieldToPlot))
 
    self.configDlg.ui.txtSampleName1.setText(self.sampleName1)
    self.configDlg.ui.txtSampleName2.setText(self.sampleName2)
    
    self.configDlg.ui.spinFigWidth.setValue(self.figWidth)
    self.configDlg.ui.spinFigHeight.setValue(self.figHeight)
    
    self.configDlg.ui.chkCustomBinWidth.setChecked(self.bCustomBinWidth)
    self.configDlg.ui.spinBinWidth.setValue(self.binWidth)     
    self.configDlg.ui.chkLogScale.setChecked(self.yAxisLogScale)
    
    self.configDlg.ui.chkCustomXaxis.setChecked(self.bCustomXaxis)
    self.configDlg.ui.spinXmin.setValue(self.xLimitLeft)
    self.configDlg.ui.spinXmax.setValue(self.xLimitRight)
    
    self.changeCustomBinWidth()
    self.changeCustomXaxis()

    self.configDlg.ui.txtXLabel.setText(self.xLabel)
    
    if self.configDlg.exec_() == QtGui.QDialog.Accepted:
      self.fieldToPlot = str(self.configDlg.ui.cboFieldToPlot.currentText())
      
      self.sampleName1 = str(self.configDlg.ui.txtSampleName1.text())
      self.sampleName2 = str(self.configDlg.ui.txtSampleName2.text())
      
      self.figWidth = self.configDlg.ui.spinFigWidth.value()
      self.figHeight = self.configDlg.ui.spinFigHeight.value()

      self.bCustomBinWidth = self.configDlg.ui.chkCustomBinWidth.isChecked()
      self.binWidth = self.configDlg.ui.spinBinWidth.value()
      self.yAxisLogScale = self.configDlg.ui.chkLogScale.isChecked()
      
      self.bCustomXaxis = self.configDlg.ui.chkCustomXaxis.isChecked()
      self.xLimitLeft = self.configDlg.ui.spinXmin.value()
      self.xLimitRight = self.configDlg.ui.spinXmax.value()

      self.xLabel = str(self.configDlg.ui.txtXLabel.text())

      self.plot(profile)
      
  def changeCustomBinWidth(self):
    self.configDlg.ui.spinBinWidth.setEnabled(self.configDlg.ui.chkCustomBinWidth.isChecked())   
    
  def changeCustomXaxis(self):
    self.configDlg.ui.spinXmin.setEnabled(self.configDlg.ui.chkCustomXaxis.isChecked())
    self.configDlg.ui.spinXmax.setEnabled(self.configDlg.ui.chkCustomXaxis.isChecked())
      
  def setXaxisMin(self):
    if self.configDlg.ui.cboFieldToPlot.currentText() == 'Sequences':    
      seqs1 = self.profile.seqList(0)
      seqs2 = self.profile.seqList(1)
    elif self.configDlg.ui.cboFieldToPlot.currentText() == 'Parental sequences':
      seqs1 = self.profile.seqList(2)
      seqs2 = self.profile.seqList(3)
      
    self.configDlg.ui.spinXmin.setValue(min(min(seqs1), min(seqs2)))
      
  def setXaxisMax(self):
    if self.configDlg.ui.cboFieldToPlot.currentText() == 'Sequences':    
      seqs1 = self.profile.seqList(0)
      seqs2 = self.profile.seqList(1)
    elif self.configDlg.ui.cboFieldToPlot.currentText() == 'Parental sequences':
      seqs1 = self.profile.seqList(2)
      seqs2 = self.profile.seqList(3)
      
    self.configDlg.ui.spinXmax.setValue(max(max(seqs1), max(seqs2)))

if __name__ == "__main__": 
  app = QtGui.QApplication(sys.argv)
  testWindow = TestWindow(SeqHistogram)
  testWindow.show()
  sys.exit(app.exec_())


        