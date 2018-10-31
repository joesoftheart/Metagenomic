# -*- coding: utf-8 -*-

# Form implementation generated from reading ui file 'multCompCorrectionInfoDlg.ui'
#
# Created: Tue Nov 24 15:27:01 2009
#      by: PyQt4 UI code generator 4.6
#
# WARNING! All changes made in this file will be lost!

from PyQt4 import QtCore, QtGui

class Ui_multCompCorrectionInfoDlg(object):
    def setupUi(self, multCompCorrectionInfoDlg):
        multCompCorrectionInfoDlg.setObjectName("multCompCorrectionInfoDlg")
        multCompCorrectionInfoDlg.resize(400, 170)
        icon = QtGui.QIcon()
        icon.addPixmap(QtGui.QPixmap("icons/programIcon.png"), QtGui.QIcon.Normal, QtGui.QIcon.Off)
        multCompCorrectionInfoDlg.setWindowIcon(icon)
        self.verticalLayout = QtGui.QVBoxLayout(multCompCorrectionInfoDlg)
        self.verticalLayout.setObjectName("verticalLayout")
        self.layout = QtGui.QFormLayout()
        self.layout.setFieldGrowthPolicy(QtGui.QFormLayout.AllNonFixedFieldsGrow)
        self.layout.setObjectName("layout")
        self.verticalLayout.addLayout(self.layout)

        self.retranslateUi(multCompCorrectionInfoDlg)
        QtCore.QMetaObject.connectSlotsByName(multCompCorrectionInfoDlg)

    def retranslateUi(self, multCompCorrectionInfoDlg):
        multCompCorrectionInfoDlg.setWindowTitle(QtGui.QApplication.translate("multCompCorrectionInfoDlg", "Additional info", None, QtGui.QApplication.UnicodeUTF8))

