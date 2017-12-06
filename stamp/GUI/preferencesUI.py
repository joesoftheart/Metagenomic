# -*- coding: utf-8 -*-

# Form implementation generated from reading ui file 'preferencesDlg.ui'
#
# Created: Tue Nov 24 15:29:44 2009
#      by: PyQt4 UI code generator 4.6
#
# WARNING! All changes made in this file will be lost!

from PyQt4 import QtCore, QtGui

class Ui_preferencesDlg(object):
    def setupUi(self, preferencesDlg):
        preferencesDlg.setObjectName("preferencesDlg")
        preferencesDlg.resize(249, 145)
        icon = QtGui.QIcon()
        icon.addPixmap(QtGui.QPixmap("icons/programIcon.png"), QtGui.QIcon.Normal, QtGui.QIcon.Off)
        preferencesDlg.setWindowIcon(icon)
        self.verticalLayout_2 = QtGui.QVBoxLayout(preferencesDlg)
        self.verticalLayout_2.setObjectName("verticalLayout_2")
        self.horizontalLayout = QtGui.QHBoxLayout()
        self.horizontalLayout.setObjectName("horizontalLayout")
        self.label = QtGui.QLabel(preferencesDlg)
        self.label.setObjectName("label")
        self.horizontalLayout.addWidget(self.label)
        self.spinPseudoCount = QtGui.QDoubleSpinBox(preferencesDlg)
        self.spinPseudoCount.setDecimals(2)
        self.spinPseudoCount.setMinimum(0.01)
        self.spinPseudoCount.setMaximum(100.0)
        self.spinPseudoCount.setSingleStep(0.1)
        self.spinPseudoCount.setProperty("value", 0.5)
        self.spinPseudoCount.setObjectName("spinPseudoCount")
        self.horizontalLayout.addWidget(self.spinPseudoCount)
        self.verticalLayout_2.addLayout(self.horizontalLayout)
        self.line = QtGui.QFrame(preferencesDlg)
        self.line.setFrameShape(QtGui.QFrame.HLine)
        self.line.setFrameShadow(QtGui.QFrame.Sunken)
        self.line.setObjectName("line")
        self.verticalLayout_2.addWidget(self.line)
        self.verticalLayout = QtGui.QVBoxLayout()
        self.verticalLayout.setObjectName("verticalLayout")
        self.chkTruncateFeatureNames = QtGui.QCheckBox(preferencesDlg)
        self.chkTruncateFeatureNames.setObjectName("chkTruncateFeatureNames")
        self.verticalLayout.addWidget(self.chkTruncateFeatureNames)
        self.horizontalLayout_3 = QtGui.QHBoxLayout()
        self.horizontalLayout_3.setObjectName("horizontalLayout_3")
        spacerItem = QtGui.QSpacerItem(40, 20, QtGui.QSizePolicy.Expanding, QtGui.QSizePolicy.Minimum)
        self.horizontalLayout_3.addItem(spacerItem)
        self.label_2 = QtGui.QLabel(preferencesDlg)
        self.label_2.setObjectName("label_2")
        self.horizontalLayout_3.addWidget(self.label_2)
        self.spinFeatureNameLength = QtGui.QSpinBox(preferencesDlg)
        self.spinFeatureNameLength.setMinimum(1)
        self.spinFeatureNameLength.setMaximum(1000)
        self.spinFeatureNameLength.setProperty("value", 10)
        self.spinFeatureNameLength.setObjectName("spinFeatureNameLength")
        self.horizontalLayout_3.addWidget(self.spinFeatureNameLength)
        self.verticalLayout.addLayout(self.horizontalLayout_3)
        self.verticalLayout_2.addLayout(self.verticalLayout)
        self.line_2 = QtGui.QFrame(preferencesDlg)
        self.line_2.setFrameShape(QtGui.QFrame.HLine)
        self.line_2.setFrameShadow(QtGui.QFrame.Sunken)
        self.line_2.setObjectName("line_2")
        self.verticalLayout_2.addWidget(self.line_2)
        self.horizontalLayout_2 = QtGui.QHBoxLayout()
        self.horizontalLayout_2.setObjectName("horizontalLayout_2")
        spacerItem1 = QtGui.QSpacerItem(40, 20, QtGui.QSizePolicy.Expanding, QtGui.QSizePolicy.Minimum)
        self.horizontalLayout_2.addItem(spacerItem1)
        self.btnOK = QtGui.QPushButton(preferencesDlg)
        self.btnOK.setObjectName("btnOK")
        self.horizontalLayout_2.addWidget(self.btnOK)
        self.verticalLayout_2.addLayout(self.horizontalLayout_2)

        self.retranslateUi(preferencesDlg)
        QtCore.QMetaObject.connectSlotsByName(preferencesDlg)

    def retranslateUi(self, preferencesDlg):
        preferencesDlg.setWindowTitle(QtGui.QApplication.translate("preferencesDlg", "Preferences", None, QtGui.QApplication.UnicodeUTF8))
        self.label.setText(QtGui.QApplication.translate("preferencesDlg", "Pseudocount for unobserved data:", None, QtGui.QApplication.UnicodeUTF8))
        self.chkTruncateFeatureNames.setText(QtGui.QApplication.translate("preferencesDlg", "Truncate feature names", None, QtGui.QApplication.UnicodeUTF8))
        self.label_2.setText(QtGui.QApplication.translate("preferencesDlg", "Length:", None, QtGui.QApplication.UnicodeUTF8))
        self.btnOK.setText(QtGui.QApplication.translate("preferencesDlg", "OK", None, QtGui.QApplication.UnicodeUTF8))

