'''
Dialog box used to assign COG categories to IMG/M profiles

@author: Donovan Parks
'''

import string

from PyQt4 import QtGui, QtCore
from metagenomics.fileIO.COG_IO import COG_IO
from assignCOGUI import Ui_AssignCOGsDlg

class AssignCOGsDlg(QtGui.QDialog):
  def __init__(self, parent=None):
    QtGui.QWidget.__init__(self, parent)
    
    # initialize GUI
    self.ui = Ui_AssignCOGsDlg()
    self.ui.setupUi(self)

    self.centerWindow()
    
    QtCore.QObject.connect(self.ui.btnLoadProfiles, QtCore.SIGNAL("clicked()"), self.loadProfiles)
    QtCore.QObject.connect(self.ui.btnCreateProfile, QtCore.SIGNAL("clicked()"), self.createProfile)
    QtCore.QObject.connect(self.ui.btnCancel, QtCore.SIGNAL("clicked()"), self.accept)
    
    self.inputProfile = []
    
  def loadProfiles(self):
    self.inputProfile = QtGui.QFileDialog.getOpenFileName(self, 'Load profile', '', 'IMG/M profiles (*.xls *.tsv);;All files (*.*)')
    if self.inputProfile != '':
      self.ui.txtInputProfile.setText(self.inputProfile)
      self.ui.btnCreateProfile.setEnabled(True)
      
  def createProfile(self):
    # get filename to save STAMP profile to
    stampFilename = QtGui.QFileDialog.getSaveFileName(self, 'Save STAMP profile...', '',
                                                              'STAMP profile file(*.spf);;All files(*.*)')
    if stampFilename == '':
      return
    
    cogIO = COG_IO()      
    cogIO.appendCategories(str(self.inputProfile), str(self.ui.cboMultiCogTreatment.currentText()), str(stampFilename))
    
    self.accept()

  def centerWindow(self):
    screen = QtGui.QDesktopWidget().screenGeometry()
    size =  self.geometry()
    self.move((screen.width()-size.width())/2, (screen.height()-size.height())/2)
