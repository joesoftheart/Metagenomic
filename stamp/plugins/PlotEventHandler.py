'''
Handle events for plots.

@author: Donovan Parks
'''

import math
from PyQt4 import QtGui

class PlotEventHandler:
  def __init__(self, xData, yData, toolTips):
    self.data = zip(xData, yData, toolTips)
    
    self.xtol = (max(xData) - min(xData)) / 100
    self.ytol = (max(yData) - min(yData)) / 100
   
  def distance(self, x1, x2, y1, y2):
    return( math.sqrt( (x1 - x2)**2 + (y1 - y2)**2 ) )

  def __call__(self, event):
    clickX = event.xdata
    clickY = event.ydata

    toolTips = []
    for x,y,tip in self.data:
      if  (event.xdata - self.xtol < x < event.xdata + self.xtol) and  (event.ydata - self.ytol < y < event.ydata + self.ytol):
        toolTips.append( (self.distance(x, clickX, y, clickY), tip) )
        
    if len(toolTips) > 0:
      toolTips.sort()
      distance, tip = toolTips[0]
      msgBox = QtGui.QMessageBox()
      
      icon = QtGui.QIcon()
      icon.addPixmap(QtGui.QPixmap("icons/programIcon.png"), QtGui.QIcon.Normal, QtGui.QIcon.Off)
      msgBox.setWindowIcon(icon)
      
      msgBox.setWindowTitle('Tooltip')
      msgBox.setText(tip);
      msgBox.exec_();