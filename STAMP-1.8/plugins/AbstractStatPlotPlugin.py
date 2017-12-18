'''
Abstract base class specifying interface of a statistical plot plugin.

@author: Donovan Parks
'''

from PyQt4 import QtGui, QtCore

from matplotlib.backends.backend_qt4agg import FigureCanvasQTAgg as FigureCanvas
from matplotlib.figure import Figure
import matplotlib.transforms as mtransforms

import numpy as np

class AbstractStatPlotPlugin(FigureCanvas):
  '''
  Abstract base class specifying interface of a statistical plot plugin.
  '''
  def __init__(self, preferences, parent=None):
    self.name = 'No name'
    
    self.fig = Figure(facecolor='white', dpi=96)
    
    FigureCanvas.__init__(self, self.fig)
    
    self.setParent(parent)
    FigureCanvas.setSizePolicy(self,
                             QtGui.QSizePolicy.Fixed,
                             QtGui.QSizePolicy.Fixed)
    FigureCanvas.updateGeometry(self)
  
  def plot(self, statsResults):
    pass
  
  def configure(self, statsResults):
    pass
  
  def mirrorProperties(self, plotToCopy):
    pass
  
  def savePlot(self, filename, dpi=600):
    format = filename[filename.rfind('.')+1:len(filename)]
    if format in ['png', 'pdf', 'ps', 'eps','svg']:      
      self.fig.savefig(filename,format=format,dpi=dpi,facecolor='white',edgecolor='white')
    else:
      pass
  
  def clear(self):
    self.fig.clear()
        
  def yLabelExtents(self, labels, fontSize):
    self.fig.clear()

    tempAxes = self.fig.add_axes([0,0,1.0,1.0])  
    tempAxes.set_yticks(np.arange(len(labels)))  
    yLabels = tempAxes.set_yticklabels(labels, size=fontSize)
    
    bboxes = []
    for label in yLabels:
      bbox = label.get_window_extent(self.get_renderer())
      bboxi = bbox.inverse_transformed(self.fig.transFigure)
      bboxes.append(bboxi)    
    yLabelBounds = mtransforms.Bbox.union(bboxes)    
    
    self.fig.clear()
    
    return yLabelBounds

  def emptyAxis(self, label = 'No active features'):
    self.fig.clear()   
    self.fig.set_size_inches(6,4)  
    emptyAxis = self.fig.add_axes([0.1,0.1,0.8,0.8]) 
    
    emptyAxis.set_ylabel(label, fontsize=8)
    emptyAxis.set_xlabel(label, fontsize=8)
    emptyAxis.set_yticks([])
    emptyAxis.set_xticks([])
    
    for loc, spine in emptyAxis.spines.iteritems():
      if loc in ['right','top']:
          spine.set_color('none') 
    
    self.updateGeometry()       
    self.draw()
    
  def formatLabels(self, labels): 
    formattedLabels = []     
    for label in labels: 
      value = float(label.get_text())    
      if value < 0.01:
        valueStr = '%.2e' % value
        if 'e-00' in valueStr:
          valueStr = valueStr.replace('e-00', 'e-')
        elif 'e-0' in valueStr:
          valueStr = valueStr.replace('e-0', 'e-')
      else:
        valueStr = '%.3f' % value
        
      formattedLabels.append(valueStr)
        
    return formattedLabels
  
class ConfigureDialog(QtGui.QDialog):
  def __init__(self, configDialogUI, parent=None):
    QtGui.QWidget.__init__(self, parent)
    
    # initialize GUI
    self.ui = configDialogUI()
    self.ui.setupUi(self)

    self.centerWindow()

  def centerWindow(self):
    screen = QtGui.QDesktopWidget().screenGeometry()
    size =  self.geometry()
    self.move((screen.width()-size.width())/2, (screen.height()-size.height())/2)
  
class TestWindow(QtGui.QMainWindow):
  '''
  Simple Qt window for testing plots.
  '''
  
  def __init__(self, PlotClass, statsResults = ''):
    '''
    Create window with plot.
    PlotClass - a class object (not instance) inherited from AbstractPlotPlugin
    '''
    
    QtGui.QMainWindow.__init__(self)
    self.setAttribute(QtCore.Qt.WA_DeleteOnClose)
    self.setWindowTitle("Test Window")
    
    self.main_widget = QtGui.QWidget(self)
    
    layout = QtGui.QVBoxLayout(self.main_widget)
    testPlot = PlotClass(self.main_widget)
    
    if statsResults != '':
      testPlot.plot(statsResults)
    else:
      testPlot.emptyAxis()
    layout.addWidget(testPlot)
    
    self.main_widget.setFocus()
    self.setCentralWidget(self.main_widget)
        