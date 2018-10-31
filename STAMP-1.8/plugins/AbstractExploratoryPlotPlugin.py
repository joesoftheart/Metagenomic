'''
Abstract base class specifying interface of a exploratory plot plugin.

@author: Donovan Parks
'''

from PyQt4 import QtGui, QtCore

from matplotlib.backends.backend_qt4agg import FigureCanvasQTAgg as FigureCanvas
from matplotlib.figure import Figure
import matplotlib.transforms as mtransforms

import numpy as np

class AbstractExploratoryPlotPlugin(FigureCanvas):
  '''
  Abstract base class specifying interface of a exploratory plot plugin.
  '''
  def __init__(self, preferences, parent=None):  
    self.preferences = preferences
    
    self.fig = Figure(facecolor='white', dpi=96)
    
    FigureCanvas.__init__(self, self.fig)
    
    self.setParent(parent)
    FigureCanvas.setSizePolicy(self,
                             QtGui.QSizePolicy.Fixed,
                             QtGui.QSizePolicy.Fixed)
    FigureCanvas.updateGeometry(self)
    
    self.cid = None
    
  def mouseEventCallback(self, callback):
    if self.cid != None:
      FigureCanvas.mpl_disconnect(self, self.cid)
      
    self.cid = FigureCanvas.mpl_connect(self, 'button_press_event', callback)
      
  def plot(self, profile):
    pass
  
  def configure(self, profile):
    pass
  
  def savePlot(self, filename, dpi=300):
    format = filename[filename.rfind('.')+1:len(filename)]
    if format in ['png', 'pdf', 'ps', 'eps','svg']:      
      self.fig.savefig(filename,format=format,dpi=dpi,facecolor='white',edgecolor='white')
    else:
      pass
  
  def clear(self):
    self.fig.clear()
    
  def mirrorProperties(self, plotToCopy):
    pass
  
  def labelExtents(self, xLabels, xFontSize, xRotation, yLabels, yFontSize, yRotation):
    self.fig.clear()
    
    tempAxes = self.fig.add_axes([0,0,1.0,1.0])  
    
    tempAxes.set_xticks(np.arange(len(xLabels)))  
    tempAxes.set_yticks(np.arange(len(yLabels)))  
    
    xText = tempAxes.set_xticklabels(xLabels, size=xFontSize, rotation=xRotation)
    yText = tempAxes.set_yticklabels(yLabels, size=yFontSize, rotation=yRotation)
    
    bboxes = []
    for label in xText:
      bbox = label.get_window_extent(self.get_renderer())
      bboxi = bbox.inverse_transformed(self.fig.transFigure)
      bboxes.append(bboxi)    
    xLabelBounds = mtransforms.Bbox.union(bboxes)
    
    bboxes = []
    for label in yText:
      bbox = label.get_window_extent(self.get_renderer())
      bboxi = bbox.inverse_transformed(self.fig.transFigure)
      bboxes.append(bboxi)    
    yLabelBounds = mtransforms.Bbox.union(bboxes)      
    
    self.fig.clear()
    
    return xLabelBounds, yLabelBounds
    
  def xLabelExtents(self, labels, fontSize, rotation=0):
    self.fig.clear()
    
    tempAxes = self.fig.add_axes([0,0,1.0,1.0])  
    tempAxes.set_xticks(np.arange(len(labels)))  
    xLabels = tempAxes.set_xticklabels(labels, size=fontSize, rotation=rotation)
    
    bboxes = []
    for label in xLabels:
      bbox = label.get_window_extent(self.get_renderer())
      bboxi = bbox.inverse_transformed(self.fig.transFigure)
      bboxes.append(bboxi)    
    xLabelBounds = mtransforms.Bbox.union(bboxes)    
    
    self.fig.clear()
    
    return xLabelBounds
  
  def yLabelExtents(self, labels, fontSize, rotation=0):
    self.fig.clear()

    tempAxes = self.fig.add_axes([0,0,1.0,1.0])  
    tempAxes.set_yticks(np.arange(len(labels)))  
    yLabels = tempAxes.set_yticklabels(labels, size=fontSize, rotation=rotation)
    
    bboxes = []
    for label in yLabels:
      bbox = label.get_window_extent(self.get_renderer())
      bboxi = bbox.inverse_transformed(self.fig.transFigure)
      bboxes.append(bboxi)    
    yLabelBounds = mtransforms.Bbox.union(bboxes)    
    
    self.fig.clear()
    
    return yLabelBounds

  def emptyAxis(self):
    self.fig.clear()   
    self.fig.set_size_inches(6,4)  
    emptyAxis = self.fig.add_axes([0.1,0.1,0.8,0.8]) 
    
    emptyAxis.set_ylabel('No active features', fontsize=8)
    emptyAxis.set_xlabel('No active features', fontsize=8)
    emptyAxis.set_yticks([])
    emptyAxis.set_xticks([])
    
    for loc, spine in emptyAxis.spines.iteritems():
      if loc in ['right','top']:
          spine.set_color('none') 
    
    self.updateGeometry()       
    self.draw()
  
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
        