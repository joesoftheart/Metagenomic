'''
Dialog box used to customize the name of hierarchical headings

@author: Donovan Parks
'''

from PyQt4 import QtGui, QtCore
from customizeHeadingsUI import Ui_CreateProfileDlg

class CustomizeHeadingsDlg(QtGui.QDialog):
  def __init__(self, parent=None):
    QtGui.QWidget.__init__(self, parent)
    
    # initialize GUI
    self.ui = Ui_CreateProfileDlg()
    self.ui.setupUi(self)

    self.centerWindow()
    
    QtCore.QObject.connect(self.ui.btnOK, QtCore.SIGNAL("clicked()"), self.accept)
    QtCore.QObject.connect(self.ui.btnCancel, QtCore.SIGNAL("clicked()"), self.reject)


  def centerWindow(self):
    screen = QtGui.QDesktopWidget().screenGeometry()
    size =  self.geometry()
    self.move((screen.width()-size.width())/2, (screen.height()-size.height())/2)
