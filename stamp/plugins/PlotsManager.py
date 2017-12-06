'''
Handle loading plot plugins along with rendering and saving of plots on the GUI.

@author: Donovan Parks
'''

from PyQt4 import QtGui

import os.path
from metagenomics.DirectoryHelper import runningExecutable

class PlotsManager:
  def __init__(self, cboPlots, plotScrollArea, defaultPlot):
    self.currentPlot = None
    self.currentPlotClass = None
    self.plotClassDict = {}
    self.plotDict = {}
    
    self.defaultPlot = defaultPlot
    
    self.cboPlots = cboPlots
    self.plotScrollArea = plotScrollArea
    
  def loadPlots(self, preferences, pluginFolder):   
    pluginModulePath = pluginFolder.replace('/', '.')
    
    if runningExecutable():
      pluginFolder = 'library/' + pluginFolder
    
    for filename in os.listdir(pluginFolder):
      if os.path.isdir(os.path.join (pluginFolder, filename)):
        continue

      extension = filename[filename.rfind('.')+1:len(filename)]  
      if extension == 'py' and filename != '__init__.py':              
        pluginModule = filename[0:filename.rfind('.')]   
        theModule = __import__(pluginModulePath + pluginModule, fromlist='*')
        theClass = getattr(theModule, pluginModule)
        plot = theClass(preferences)
        self.cboPlots.addItem(plot.name)
        
        self.plotClassDict[plot.name] = theClass
        self.plotDict[plot.name] = plot
        
    self.display(self.defaultPlot, None)
    self.cboPlots.setCurrentIndex(self.cboPlots.findText(self.defaultPlot)) 
    
  def display(self, plotName, data):    
    # remove current plot widget
    if self.currentPlot != None:
      widget = self.plotScrollArea.takeWidget()
      widget.setParent(None)
      del widget
    
    # add new plot widget
    self.currentPlotClass = self.plotClassDict[plotName]
    self.currentPlot = self.plotDict[plotName]
    if data != None:
      self.currentPlot.plot(data)
    else:
      self.currentPlot.emptyAxis()    
    self.plotScrollArea.setWidget(self.currentPlot)
    
  def update(self, data):
    self.display(str(self.cboPlots.currentText()), data)
    
  def reset(self, preferences):
    for plotName in self.plotClassDict:
      theClass = self.plotClassDict[plotName]
      self.plotDict[plotName] = theClass(preferences)
    
  def configure(self, data):
    self.currentPlot.configure(data)
    
  def save(self, file, dpi = 300):
    self.currentPlot.savePlot(str(file), dpi)
    
  def sendToNewWindow(self, data):   
    newPlotWindow = self.currentPlotClass(self.currentPlot.preferences)
    newPlotWindow.mirrorProperties(self.currentPlot)
    newPlotWindow.plot(data)
    
    w, h = newPlotWindow.get_width_height()
    newPlotWindow.setFixedSize(w, h)
    
    newPlotWindow.setWindowTitle(self.cboPlots.currentText())    
    newPlotWindow.show()  
          
    icon = QtGui.QIcon()
    icon.addPixmap(QtGui.QPixmap("icons/programIcon.png"), QtGui.QIcon.Normal, QtGui.QIcon.Off)
    newPlotWindow.setWindowIcon(icon)
    
    return newPlotWindow
