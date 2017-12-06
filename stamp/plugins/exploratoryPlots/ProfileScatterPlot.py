'''
Profile scatter plot

@author: Donovan Parks
'''

import sys

from PyQt4 import QtGui, QtCore

from plugins.AbstractExploratoryPlotPlugin import AbstractExploratoryPlotPlugin, TestWindow, ConfigureDialog
from plugins.exploratoryPlots.configGUI.profileScatterPlotUI import Ui_ProfileScatterPlotDialog

from plugins.PlotEventHandler import PlotEventHandler

from matplotlib.lines import Line2D
from matplotlib.ticker import NullFormatter

from metagenomics.stats.distributions.NormalDist import inverseNormalCDF
from metagenomics.stats.CI.WilsonCI import WilsonCI

class ProfileScatterPlot(AbstractExploratoryPlotPlugin):
  '''
  Profile scatter plot.
  '''
  def __init__(self, preferences, parent=None):
    AbstractExploratoryPlotPlugin.__init__(self, preferences, parent)
    self.preferences = preferences
   
    self.name = 'Profile scatter plot'
    self.figWidth = 6.0
    self.figHeight = 6.0
    
    self.sampleName1 = ''
    self.sampleName2 = ''
    
    self.bShowCIs = True
    self.endCapSize = 0

    self.numBins = 30
    self.histogramSize = 0.5
    self.bShowHistograms = True
    
  def mirrorProperties(self, plotToCopy):
    self.name = plotToCopy.name
    self.figWidth = plotToCopy.figWidth
    self.figHeight = plotToCopy.figHeight
    self.sampleName1 = plotToCopy.sampleName1
    self.sampleName2 = plotToCopy.sampleName2
    self.bShowCIs = plotToCopy.bShowCIs
    self.endCapSize = plotToCopy.endCapSize
    self.numBins = plotToCopy.numBins
    self.histogramSize = plotToCopy.histogramSize
    self.bShowHistograms = plotToCopy.bShowHistograms
    
  def plot(self, profile):
    if len(profile.profileDict) <= 0:
      self.emptyAxis()      
      return

    if len(profile.profileDict) > 1000:
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
    for table in tables:
      feature, seq1, seq2, parentSeq1, parentSeq2 = table
      features.append(feature)
      field1.append(float(seq1)*100 / max(parentSeq1,1))
      field2.append(float(seq2)*100 / max(parentSeq2,1))
      
      if self.bShowCIs:
        lowerCI, upperCI, p = wilsonCI.run(seq1, parentSeq1, 0.95, zCoverage)
        confInter1.append([max(lowerCI*100, 0), min(upperCI*100,100)])
        
        lowerCI, upperCI, p = wilsonCI.run(seq2, parentSeq2, 0.95, zCoverage)
        confInter2.append([max(lowerCI*100, 0), min(upperCI*100,100)])
          
    # *** Set figure size
    self.fig.clear()
    self.fig.set_size_inches(self.figWidth, self.figHeight)  
    
    if self.bShowHistograms:
        histogramSizeX = self.histogramSize /self.figWidth
        histogramSizeY = self.histogramSize /self.figHeight
    else:
        histogramSizeX = 0.0
        histogramSizeY = 0.0

    padding = 0.1           # inches
    xOffsetFigSpace = (0.4 + padding)/self.figWidth
    yOffsetFigSpace = (0.3 + padding)/self.figHeight
    axesScatter = self.fig.add_axes([xOffsetFigSpace, yOffsetFigSpace,
                                    1.0 - xOffsetFigSpace - histogramSizeX - (2*padding)/self.figWidth, 1.0 - yOffsetFigSpace - histogramSizeY - (2*padding)/self.figHeight])

    if self.bShowHistograms:
        axesTopHistogram = self.fig.add_axes([xOffsetFigSpace, 1.0 - histogramSizeY - padding/self.figHeight,
                                    1.0 - xOffsetFigSpace - histogramSizeX - (2*padding)/self.figWidth, histogramSizeY])
    
        axesRightHistogram = self.fig.add_axes([1.0 - histogramSizeX - padding/self.figWidth, yOffsetFigSpace,
                                    histogramSizeX, 1.0 - yOffsetFigSpace - histogramSizeY - (2*padding)/self.figHeight])
    
    # *** Handle mouse events
    tooltips = []
    for i in xrange(0, len(field1)):
      tooltip = features[i] + '\n\n'
      tooltip += 'Sequences in ' + self.sampleName1 + ': ' + str(tables[i][1]) + '\n'
      tooltip += 'Sequences in ' + self.sampleName2 + ': ' + str(tables[i][2]) + '\n\n' 
      tooltip += (self.sampleName1 + ' percentage: %.3f' % field1[i]) + '\n' 
      tooltip += (self.sampleName2 + ' percentage: %.3f' % field2[i]) + '\n\n' 
      tooltip += 'Difference between proportions (%): ' + ('%.3f' % (field1[i] - field2[i])) + '\n'
      
      if field2[i] != 0:
        tooltip += 'Ratio of proportions: %.3f' % (field1[i]/field2[i])
      else:
        tooltip += 'Ratio of proportions: undefined'
      tooltips.append(tooltip)
      
    self.plotEventHandler =  PlotEventHandler(field1, field2, tooltips)
    
    self.mouseEventCallback(self.plotEventHandler)
    
    # *** Plot data
    
    # set visual properties of all points
    colours = []
    highlightedField1 = []
    highlightedField2 = []
    highlighColours = []
    for i in xrange(0, len(field1)):
      if field1[i] > field2[i]:
        colours.append(profile1Colour)    
      else:
        colours.append(profile2Colour)
        
      if features[i] in self.preferences['Selected exploratory features']:
        highlightedField1.append(field1[i])
        highlightedField2.append(field2[i])
        highlighColours.append(colours[i])     
    
    # scatter plot  
    axesScatter.scatter(field1, field2, c=colours, zorder=5)
    if len(highlightedField1) > 0:
      axesScatter.scatter(highlightedField1, highlightedField2, c=highlighColours, edgecolors = 'red', linewidth = 2, zorder=10)  
    
    # plot CIs
    if self.bShowCIs:
      ciLinesX = [Line2D([confInter1[i][0],confInter1[i][1]],[field2[i],field2[i]],color='black') for i in xrange(0, len(field1))]
      ciLinesY = [Line2D([field1[i],field1[i]],[confInter2[i][0],confInter2[i][1]],color='black') for i in xrange(0, len(field1))]
      
      for i in xrange(0, len(ciLinesX)):
        e = ciLinesX[i]
        axesScatter.add_artist(e)
        e.set_clip_box(axesScatter.bbox)
        
      for i in xrange(0, len(ciLinesY)):
        e = ciLinesY[i]
        axesScatter.add_artist(e)
        e.set_clip_box(axesScatter.bbox)
      
    # plot y=x line
    maxProportion = max(max(field1),max(field2))*1.05
    axesScatter.plot([0,maxProportion],[0,maxProportion], color='gray', linestyle='dashed', marker='', zorder = 1)
    
    axesScatter.set_xlabel(self.sampleName1 + ' (%)', fontsize=8)
    axesScatter.set_ylabel(self.sampleName2 + ' (%)', fontsize=8)
        
    axesScatter.set_xlim(0, maxProportion)
    axesScatter.set_ylim(0, maxProportion)
    
    # *** Prettify scatter plot         
    for label in axesScatter.get_xticklabels():
      label.set_size(8)
        
    for label in axesScatter.get_yticklabels():
      label.set_size(8)

    # plot histograms
    if self.bShowHistograms:
        # plot top histogram
        axesTopHistogram.xaxis.set_major_formatter(NullFormatter())
        pdf, bins, patches = axesTopHistogram.hist(field1, bins = self.numBins, facecolor = profile1Colour)
        axesTopHistogram.set_xlim(axesScatter.get_xlim())
        axesTopHistogram.set_yticks([0, max(pdf)])

        # plot right histogram
        axesRightHistogram.yaxis.set_major_formatter(NullFormatter())
        pdf, bins, patches = axesRightHistogram.hist(field2, bins = self.numBins, orientation='horizontal', facecolor = profile2Colour)
        axesRightHistogram.set_ylim(axesScatter.get_ylim())
        axesRightHistogram.set_xticks([0, max(pdf)])

        # *** Prettify histogram plot         
        for label in axesTopHistogram.get_xticklabels():
            label.set_size(8)
        
        for label in axesTopHistogram.get_yticklabels():
            label.set_size(8)

        for label in axesRightHistogram.get_xticklabels():
            label.set_size(8)
        
        for label in axesRightHistogram.get_yticklabels():
            label.set_size(8)

        for a in axesTopHistogram.yaxis.majorTicks:
            a.tick1On=True
            a.tick2On=False
          
        for a in axesTopHistogram.xaxis.majorTicks:
            a.tick1On=True
            a.tick2On=False

        for loc, spine in axesTopHistogram.spines.iteritems():
            if loc in ['right','top']:
                spine.set_color('none') 

        for a in axesRightHistogram.yaxis.majorTicks:
            a.tick1On=True
            a.tick2On=False
          
        for a in axesRightHistogram.xaxis.majorTicks:
            a.tick1On=True
            a.tick2On=False

        for loc, spine in axesRightHistogram.spines.iteritems():
            if loc in ['right','top']:
                spine.set_color('none') 

    self.updateGeometry()       
    self.draw()

  def configure(self, profile):
    configDlg = ConfigureDialog(Ui_ProfileScatterPlotDialog)
    
    configDlg.ui.txtSampleName1.setText(self.sampleName1)
    configDlg.ui.txtSampleName2.setText(self.sampleName2)
        
    configDlg.ui.spinFigWidth.setValue(self.figWidth)
    configDlg.ui.spinFigHeight.setValue(self.figHeight)
    
    configDlg.ui.chkShowCIs.setChecked(self.bShowCIs)
    configDlg.ui.spinEndCapSize.setValue(self.endCapSize)

    configDlg.ui.spinNumBins.setValue(self.numBins)
    configDlg.ui.spinHistogramSize.setValue(self.histogramSize)
    configDlg.ui.chkShowHistogram.setChecked(self.bShowHistograms)
        
    if configDlg.exec_() == QtGui.QDialog.Accepted:   

      self.sampleName1 = str(configDlg.ui.txtSampleName1.text())
      self.sampleName2 = str(configDlg.ui.txtSampleName2.text())
      
      self.figWidth = configDlg.ui.spinFigWidth.value()
      self.figHeight = configDlg.ui.spinFigHeight.value()
      
      self.bShowCIs = configDlg.ui.chkShowCIs.isChecked()
      self.endCapSize = configDlg.ui.spinEndCapSize.value()
      
      self.numBins = configDlg.ui.spinNumBins.value()
      self.histogramSize = configDlg.ui.spinHistogramSize.value()
      self.bShowHistograms = configDlg.ui.chkShowHistogram.isChecked()

      self.plot(profile)
          
if __name__ == "__main__": 
  app = QtGui.QApplication(sys.argv)
  testWindow = TestWindow(ProfileScatterPlot)
  testWindow.show()
  sys.exit(app.exec_())