'''
Dialog box used to set user specified preferences.

@author: Donovan Parks
'''

from PyQt4 import QtGui, QtCore
from preferencesUI import Ui_preferencesDlg

class PreferencesDlg(QtGui.QDialog):
  def __init__(self, parent=None, info=None):
    QtGui.QWidget.__init__(self, parent)
    
    # initialize GUI
    self.ui = Ui_preferencesDlg()
    self.ui.setupUi(self)

    self.centerWindow()
    
    self.tuncFeatureNameChanged()
    
    # connect signals to slots
    self.connect(self.ui.chkTruncateFeatureNames, QtCore.SIGNAL('toggled(bool)'), self.tuncFeatureNameChanged)
    self.connect(self.ui.btnOK, QtCore.SIGNAL("clicked()"), self.accept)
    
  def centerWindow(self):
    screen = QtGui.QDesktopWidget().screenGeometry()
    size =  self.geometry()
    self.move((screen.width()-size.width())/2, (screen.height()-size.height())/2)
        
  def tuncFeatureNameChanged(self):
    self.ui.spinFeatureNameLength.setEnabled(self.ui.chkTruncateFeatureNames.isChecked())
    
if __name__ == "__main__": 
  pass