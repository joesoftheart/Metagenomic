'''
Extended error bar plot.

@author: Donovan Parks
'''

from PyQt4 import QtGui, QtCore

import sys
import math
import numpy as np
from mpl_toolkits.axes_grid import make_axes_locatable, Size

from plugins.AbstractStatPlotPlugin import AbstractStatPlotPlugin, TestWindow, ConfigureDialog
from plugins.statPlots.configGUI.extendedErrorBarUI import Ui_ExtendedErrorBarDialog
from metagenomics import TableHelper
    
class ExtendedErrorBar(AbstractStatPlotPlugin):
  '''
  Extended error bar plot.
  '''   
  def __init__(self, preferences, parent=None):
    AbstractStatPlotPlugin.__init__(self, preferences, parent)
    self.preferences = preferences
    
    self.name = 'Extended error bar'
    
    self.figWidth = 8.5
    self.figHeightPerRow = 1.0/5.0
    
    self.sortingField = 'p-values'
    
    self.bShowSeqPlot = True
    self.bShowPowerPlot = False   # temporarily disabled
    self.bShowPValueLabels = True
    
    self.bShowCorrectedPvalues = True
    
    self.bCustomLimits = False
    self.minX = None
    self.maxX = None

    self.xLabel = 'Sequences'
    
  def mirrorProperties(self, plotToCopy):
    self.name = plotToCopy.name
    
    self.figWidth = plotToCopy.figWidth
    self.figHeightPerRow = plotToCopy.figHeightPerRow
    
    self.sortingField = plotToCopy.sortingField
    
    self.bShowSeqPlot = plotToCopy.bShowSeqPlot
    self.bShowPowerPlot = plotToCopy.bShowPowerPlot
    self.bShowPValueLabels = plotToCopy.bShowPValueLabels
    
    self.bShowCorrectedPvalues = plotToCopy.bShowCorrectedPvalues
    
    self.bCustomLimits = plotToCopy.bCustomLimits
    self.minX = plotToCopy.minX
    self.maxX = plotToCopy.maxX

    self.xLabel = plotToCopy.xLabel

  
  def plot(self, statsResults):
    '''
    Create extended error bar plot.
      filename: name of output file
      data: matrix of data for each feature of interest; [feature, pValue, effectSize, lowerCI, upperCI, seq1, seq1, power]
      oneMinusAlpha: 1-alpha value used to create above data matrix
    '''
    
    # *** Check if there is sufficient data to generate the plot
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
            
    # *** Colour of plot elements
    highlightColor = (0.9, 0.9, 0.9)
    orangeColour = (1,0.5,0)   
    
    # *** Sort data
    if self.sortingField == 'p-values':
      statsResults.activeData = TableHelper.SortTable(statsResults.activeData,\
                                                        [statsResults.dataHeadings['pValues']], False)
    elif self.sortingField == 'Effect sizes':
      statsResults.activeData = TableHelper.SortTable(statsResults.activeData,\
                                                        [statsResults.dataHeadings['EffectSize']], 
                                                        True, True, statsResults.confIntervMethod.bRatio)
      
    elif self.sortingField == 'Feature labels':
      statsResults.activeData = TableHelper.SortTableStrCol(statsResults.activeData,\
                                                        statsResults.dataHeadings['Features'], False)
          
    # *** Create lists for each quantity of interest
    features = statsResults.getColumn('Features')
		
    if statsResults.multCompCorrection.method == 'False discovery rate':
      pValueTitle = 'q-value'
    else:
      pValueTitle = 'p-value'

    if self.bShowCorrectedPvalues:
      pValueLabels = statsResults.getColumnAsStr('pValuesCorrected')
      pValueTitle += ' (corrected)'
    else:
      pValueLabels = statsResults.getColumnAsStr('pValues')
      
    effectSizes = statsResults.getColumn('EffectSize')
    
    lowerCIs = statsResults.getColumn('LowerCI')
    upperCIs = statsResults.getColumn('UpperCI')
    ciTitle = ('%.3g' % (statsResults.oneMinusAlpha()*100)) + '% confidence intervals'
      
    seqs1 = statsResults.getColumn('Seq1')
    seqs2 = statsResults.getColumn('Seq2')
    power = statsResults.getColumn('Power')
    
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
        
    # *** Adjust effect size for axis scale
    dominateInSample2 = []
    for i in xrange(0, len(effectSizes)):
      seqs1[i] = -seqs1[i]
      
      if statsResults.confIntervMethod.bRatio:
        if effectSizes[i] < 1:
          # mirror CI across y-axis
          effectSizes[i] = 1.0 / effectSizes[i]
          lowerCI = effectSizes[i] - (1.0 / upperCIs[i])
          upperCI = (1.0 / lowerCIs[i]) - effectSizes[i]     
                           
          lowerCIs[i] = lowerCI
          upperCIs[i] = upperCI
          
          dominateInSample2.append(i)
        else:
          lowerCIs[i] = effectSizes[i] - lowerCIs[i]
          upperCIs[i] = upperCIs[i] - effectSizes[i] 
      else:
        lowerCIs[i] = effectSizes[i] - lowerCIs[i]
        upperCIs[i] = upperCIs[i] - effectSizes[i]   
        if effectSizes[i] < 0.0:
          dominateInSample2.append(i)

    # *** Determine which axes should be created
    bShowPowerPlot = self.bShowPowerPlot and len(power) != 0 and not math.isnan(power[0])
           
    # *** Set figure size
    plotHeight = self.figHeightPerRow*len(features) 
    self.imageWidth = self.figWidth
    self.imageHeight = plotHeight  + 0.65   # 0.65 inches for bottom and top labels
    if self.imageWidth > 256 or self.imageHeight > 256:
        QtGui.QApplication.instance().setOverrideCursor(QtGui.QCursor(QtCore.Qt.ArrowCursor))
        self.emptyAxis()  
        reply = QtGui.QMessageBox.question(self, 'Excessively large plot', 'The resulting plot is too large to display.')
        QtGui.QApplication.instance().restoreOverrideCursor()
        return
		
    self.fig.set_size_inches(self.imageWidth, self.imageHeight)  
        
    # *** Determine width of y-axis labels
    yLabelBounds = self.yLabelExtents(features, 8)
    
    # *** Size plots which comprise the extended errorbar plot
    self.fig.clear()     
    
    spacingBetweenPlots = 0.3       # inches  
    heightBottomLabels = 0.4        # inches  
    totalSpacingBetweenPlots = spacingBetweenPlots*2  # inches
    
    widthNumSeqPlot = 1.25        # inches
    if self.bShowSeqPlot == False:
      widthNumSeqPlot = 0.0
      totalSpacingBetweenPlots -= spacingBetweenPlots
    
    widthPowerPlot = 0.75         # inches
    if bShowPowerPlot == False:
      widthPowerPlot = 0.0
      totalSpacingBetweenPlots -= spacingBetweenPlots
    
    widthPvalueLabels = 0.75      # inches
    if self.bShowPValueLabels == False:
      widthPvalueLabels = 0.1
         
    yPlotOffsetFigSpace = heightBottomLabels / self.imageHeight 
    heightPlotFigSpace = plotHeight / self.imageHeight
       
    xPlotOffsetFigSpace = yLabelBounds.width + 0.1 / self.imageWidth
    pValueLabelWidthFigSpace =  widthPvalueLabels / self.imageWidth
    widthPlotFigSpace = 1.0 - pValueLabelWidthFigSpace - xPlotOffsetFigSpace
    
    widthErrorBarPlot = widthPlotFigSpace*self.imageWidth - widthNumSeqPlot - widthPowerPlot - totalSpacingBetweenPlots
        
    axInitAxis = self.fig.add_axes([xPlotOffsetFigSpace,yPlotOffsetFigSpace,widthPlotFigSpace,heightPlotFigSpace])    
    divider = make_axes_locatable(axInitAxis)  
    divider.get_vertical()[0] = Size.Fixed(len(features)*self.figHeightPerRow)
   
    if self.bShowSeqPlot == True:   
      divider.get_horizontal()[0] = Size.Fixed(widthNumSeqPlot)
      axErrorbar = divider.new_horizontal(widthErrorBarPlot, pad=spacingBetweenPlots, sharey=axInitAxis)
      self.fig.add_axes(axErrorbar)
    else:
      divider.get_horizontal()[0] = Size.Fixed(widthErrorBarPlot)
      axErrorbar = axInitAxis
    
    if bShowPowerPlot == True:
      axPower = divider.new_horizontal(widthPowerPlot, pad=spacingBetweenPlots, sharey=axInitAxis)
      self.fig.add_axes(axPower)
        
    # *** Plot of sequences for each subsystem
    if self.bShowSeqPlot == True:      
      axNumSeq = axInitAxis
      
      axNumSeq.hlines(np.arange(len(features)), [0], seqs1, lw=4, color=profile1Colour, zorder=10)
      axNumSeq.hlines(np.arange(len(features)), [0], seqs2, lw=4, color=profile2Colour, zorder=10)
      for value in np.arange(-0.5, len(features)-1, 2):
        axNumSeq.axhspan(value, value+1, facecolor=highlightColor,edgecolor='none',zorder=1)
        
      axNumSeq.vlines(0, -1, len(features), color='black', zorder=10)
      
      axNumSeq.set_xlabel(self.xLabel, fontsize=8)
      axNumSeq.set_xticks([min(seqs1), 0, max(seqs2)])
      axNumSeq.set_xticklabels([-min(seqs1), 0, max(seqs2)])
      
      axNumSeq.set_yticks(np.arange(len(features)))
      axNumSeq.set_yticklabels(features, size=8)      
      axNumSeq.set_ylim([-1, len(features)])
      
      for label in axNumSeq.get_xticklabels():
        label.set_size(8)
          
      for label in axNumSeq.get_yticklabels():
        label.set_size(8)
        if label.get_text() in selectedFeatures:
          label.set_color('red')
          
      for a in axNumSeq.yaxis.majorTicks:
        a.tick1On=False
        a.tick2On=False
          
      for a in axNumSeq.xaxis.majorTicks:
        a.tick1On=True
        a.tick2On=False
          
      for loc, spine in axNumSeq.spines.iteritems():
        if loc in ['left', 'right','top']:
            spine.set_color('none') 
            
    # *** Plot confidence intervals for each subsystem
    lastAxes = axErrorbar        
    axErrorbar.errorbar(effectSizes, np.arange(len(features)), xerr=[lowerCIs,upperCIs], fmt='o', mfc=profile1Colour, mec='black', ecolor='black', zorder=10)
    effectSizesSample2 = [effectSizes[value] for value in dominateInSample2]
    axErrorbar.plot(effectSizesSample2, dominateInSample2, ls='', marker='o', mfc=profile2Colour, mec='black', zorder=100)
    
    if statsResults.confIntervMethod.bRatio:
      axErrorbar.vlines(1, -1, len(features), linestyle='dashed', color=(0.25,0.25,0.25))
    else:
      axErrorbar.vlines(0, -1, len(features), linestyle='dashed', color=(0.25,0.25,0.25))
    
    for value in np.arange(-0.5, len(features)-1, 2):
      axErrorbar.axhspan(value, value+1, facecolor=highlightColor,edgecolor='none',zorder=1)

    axErrorbar.set_title(ciTitle, fontsize=8) 
    axErrorbar.set_xlabel(statsResults.confIntervMethod.plotLabel, fontsize=8)
    
    if self.bCustomLimits:
      axErrorbar.set_xlim([self.minX, self.maxX])
    else:
      self.minX, self.maxX = axErrorbar.get_xlim()
           
    if self.bShowSeqPlot == False:
      axErrorbar.set_yticks(np.arange(len(features)))
      axErrorbar.set_yticklabels(features, size=8)
      axErrorbar.set_ylim([-1, len(features)])
      
      for label in axErrorbar.get_yticklabels():
        label.set_size(8)
        if label.get_text() in self.preferences['Selected statistical features']:
          label.set_color('red')
    else:
      for label in axErrorbar.get_yticklabels():
        label.set_visible(False)
        
      for a in axErrorbar.yaxis.majorTicks:
        a.set_visible(False)
                
    for label in axErrorbar.get_xticklabels():
      label.set_size(8)          
        
    for a in axErrorbar.xaxis.majorTicks:
      a.tick1On=True
      a.tick2On=False
        
    for a in axErrorbar.yaxis.majorTicks:
      a.tick1On=False
      a.tick2On=False
        
    for loc, spine in axErrorbar.spines.iteritems():
      if loc in ['left','right','top']:
        if loc != 'left' or (loc == 'left' and (self.bShowSeqPlot == True or bShowPowerPlot == True or self.bShowPValueLabels == False)):
          spine.set_color('none') 
            
    # *** Plot results of power test for each subsystem
    if bShowPowerPlot == True:
      lastAxes = axPower
      axPower.scatter(power, np.arange(len(features)), s=20, facecolor=orangeColour, edgecolor='none', marker='^', linewidth=1, zorder=10)
      axPower.vlines(0.5, -1, len(features), linestyle='dashed', color=(0.25,0.25,0.25))
      for value in np.arange(-0.5, len(features)-1, 2):
        axPower.axhspan(value, value+1, facecolor=highlightColor,edgecolor='none',zorder=1)
      axPower.set_xlabel('Power',fontsize=8)
      axPower.set_xticks([0, 0.5, 1.0])
      axPower.set_xlim(-0.1,1.1)
      axPower.set_ylim(-1,len(features))
                  
      for label in axPower.get_xticklabels():
        label.set_size(8)
          
      for label in axPower.get_yticklabels():
        label.set_visible(False)
          
      for a in axPower.yaxis.majorTicks:
        a.set_visible(False)
          
      for a in axPower.xaxis.majorTicks:
        a.tick1On=True
        a.tick2On=False
          
      for loc, spine in axPower.spines.iteritems():
        if loc in ['left', 'right','top']:
            spine.set_color('none') 
                
    # *** Show p-values on right of last plot
    if self.bShowPValueLabels == True:
      axRight = lastAxes.twinx()
      axRight.set_yticks(np.arange(len(pValueLabels)))
      axRight.set_yticklabels(pValueLabels, size=8)
      axRight.set_ylim([-1, len(pValueLabels)])
      axRight.set_ylabel(pValueTitle, fontsize=8)
      if bShowPowerPlot == True:
        axRight.set_xticks([0, 0.5, 1.0])
      
      for a in axRight.yaxis.majorTicks:
        a.tick1On=False
        a.tick2On=False
      
      for label in axRight.get_yticklabels():
        label.set_size(8)
      
      for loc, spine in axRight.spines.iteritems():
        if loc in ['left', 'right','top']:
            spine.set_color('none') 
            
    self.updateGeometry()       
    self.draw()
                    
  def configure(self, statsResults):
    self.statsResults = statsResults
    
    self.configDlg = ConfigureDialog(Ui_ExtendedErrorBarDialog)

    self.configDlg.ui.cboSortingField.setCurrentIndex(self.configDlg.ui.cboSortingField.findText(self.sortingField))
    
    self.configDlg.ui.spinFigWidth.setValue(self.figWidth)
    self.configDlg.ui.spinFigRowHeight.setValue(self.figHeightPerRow)
    
    self.configDlg.ui.chkShowSeq.setChecked(self.bShowSeqPlot)
    #self.configDlg.ui.chkShowPower.setChecked(self.bShowPowerPlot)
    self.configDlg.ui.chkPValueLabels.setChecked(self.bShowPValueLabels)
    
    self.configDlg.ui.chkCorrectedPvalues.setChecked(self.bShowCorrectedPvalues)
    
    self.configDlg.ui.chkCustomLimits.setChecked(self.bCustomLimits)
    self.configDlg.ui.spinMinimumX.setValue(self.minX)
    self.configDlg.ui.spinMaximumX.setValue(self.maxX)

    self.configDlg.ui.txtXLabel.setText(self.xLabel)
    
    if self.configDlg.exec_() == QtGui.QDialog.Accepted:
      QtGui.QApplication.instance().setOverrideCursor(QtGui.QCursor(QtCore.Qt.WaitCursor))
      
      self.sortingField = str(self.configDlg.ui.cboSortingField.currentText())
      
      self.figWidth = self.configDlg.ui.spinFigWidth.value()
      self.figHeightPerRow = self.configDlg.ui.spinFigRowHeight.value()
      
      self.bShowSeqPlot = self.configDlg.ui.chkShowSeq.isChecked()
      #self.bShowPowerPlot = self.configDlg.ui.chkShowPower.isChecked()
      self.bShowPValueLabels = self.configDlg.ui.chkPValueLabels.isChecked()
      
      self.bShowCorrectedPvalues = self.configDlg.ui.chkCorrectedPvalues.isChecked()
      
      self.bCustomLimits = self.configDlg.ui.chkCustomLimits.isChecked()
      self.minX = self.configDlg.ui.spinMinimumX.value()
      self.maxX = self.configDlg.ui.spinMaximumX.value()
      
      self.xLabel = str(self.configDlg.ui.txtXLabel.text())

      self.plot(statsResults)    
      
      QtGui.QApplication.instance().restoreOverrideCursor()   

if __name__ == "__main__": 
  app = QtGui.QApplication(sys.argv)
  testWindow = TestWindow(ExtendedErrorBar)
  testWindow.show()
  sys.exit(app.exec_())
