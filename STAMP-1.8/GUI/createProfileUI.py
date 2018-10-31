# -*- coding: utf-8 -*-

# Form implementation generated from reading ui file 'createProfile.ui'
#
# Created: Sun Mar 13 16:00:38 2011
#      by: PyQt4 UI code generator 4.6.2
#
# WARNING! All changes made in this file will be lost!

from PyQt4 import QtCore, QtGui

class Ui_CreateProfileDlg(object):
    def setupUi(self, CreateProfileDlg):
        CreateProfileDlg.setObjectName("CreateProfileDlg")
        CreateProfileDlg.resize(259, 156)
        icon = QtGui.QIcon()
        icon.addPixmap(QtGui.QPixmap("../icons/programIcon.png"), QtGui.QIcon.Normal, QtGui.QIcon.Off)
        CreateProfileDlg.setWindowIcon(icon)
        self.verticalLayout = QtGui.QVBoxLayout(CreateProfileDlg)
        self.verticalLayout.setObjectName("verticalLayout")
        self.horizontalLayout_2 = QtGui.QHBoxLayout()
        self.horizontalLayout_2.setObjectName("horizontalLayout_2")
        self.label = QtGui.QLabel(CreateProfileDlg)
        self.label.setObjectName("label")
        self.horizontalLayout_2.addWidget(self.label)
        self.cboProfileType = QtGui.QComboBox(CreateProfileDlg)
        self.cboProfileType.setObjectName("cboProfileType")
        self.cboProfileType.addItem("")
        self.cboProfileType.addItem("")
        self.horizontalLayout_2.addWidget(self.cboProfileType)
        spacerItem = QtGui.QSpacerItem(40, 20, QtGui.QSizePolicy.Expanding, QtGui.QSizePolicy.Minimum)
        self.horizontalLayout_2.addItem(spacerItem)
        self.verticalLayout.addLayout(self.horizontalLayout_2)
        self.btnLoadProfiles = QtGui.QPushButton(CreateProfileDlg)
        self.btnLoadProfiles.setObjectName("btnLoadProfiles")
        self.verticalLayout.addWidget(self.btnLoadProfiles)
        self.btnCustomizeHeadings = QtGui.QPushButton(CreateProfileDlg)
        self.btnCustomizeHeadings.setEnabled(False)
        self.btnCustomizeHeadings.setObjectName("btnCustomizeHeadings")
        self.verticalLayout.addWidget(self.btnCustomizeHeadings)
        self.btnCreateProfile = QtGui.QPushButton(CreateProfileDlg)
        self.btnCreateProfile.setEnabled(False)
        self.btnCreateProfile.setObjectName("btnCreateProfile")
        self.verticalLayout.addWidget(self.btnCreateProfile)
        self.btnCancel = QtGui.QPushButton(CreateProfileDlg)
        self.btnCancel.setObjectName("btnCancel")
        self.verticalLayout.addWidget(self.btnCancel)

        self.retranslateUi(CreateProfileDlg)
        QtCore.QMetaObject.connectSlotsByName(CreateProfileDlg)

    def retranslateUi(self, CreateProfileDlg):
        CreateProfileDlg.setWindowTitle(QtGui.QApplication.translate("CreateProfileDlg", "Create profile", None, QtGui.QApplication.UnicodeUTF8))
        self.label.setText(QtGui.QApplication.translate("CreateProfileDlg", "Profile type:", None, QtGui.QApplication.UnicodeUTF8))
        self.cboProfileType.setItemText(0, QtGui.QApplication.translate("CreateProfileDlg", "MG-RAST functional profile", None, QtGui.QApplication.UnicodeUTF8))
        self.cboProfileType.setItemText(1, QtGui.QApplication.translate("CreateProfileDlg", "MG-RAST organism profile", None, QtGui.QApplication.UnicodeUTF8))
        self.btnLoadProfiles.setText(QtGui.QApplication.translate("CreateProfileDlg", "Load profile", None, QtGui.QApplication.UnicodeUTF8))
        self.btnCustomizeHeadings.setText(QtGui.QApplication.translate("CreateProfileDlg", "Customize headings", None, QtGui.QApplication.UnicodeUTF8))
        self.btnCreateProfile.setText(QtGui.QApplication.translate("CreateProfileDlg", "Create STAMP profile", None, QtGui.QApplication.UnicodeUTF8))
        self.btnCancel.setText(QtGui.QApplication.translate("CreateProfileDlg", "Cancel", None, QtGui.QApplication.UnicodeUTF8))

