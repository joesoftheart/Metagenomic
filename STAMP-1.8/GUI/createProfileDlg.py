'''
Dialog box used to create STAMP profiles

@author: Donovan Parks
'''

import string

from PyQt4 import QtGui, QtCore
from createProfileUI import Ui_CreateProfileDlg
from GUI.customizeHeadingsDlg import CustomizeHeadingsDlg

class ProfileRow():
	def __init__(self):
		countData = []
		hierarchy = []

class CreateProfileDlg(QtGui.QDialog):
	def __init__(self, parent=None):
		QtGui.QWidget.__init__(self, parent)
		
		# initialize GUI
		self.ui = Ui_CreateProfileDlg()
		self.ui.setupUi(self)

		self.centerWindow()
		
		QtCore.QObject.connect(self.ui.cboProfileType, QtCore.SIGNAL('activated(QString)'), self.changeProfileType)
		QtCore.QObject.connect(self.ui.btnLoadProfiles, QtCore.SIGNAL("clicked()"), self.loadProfiles)
		QtCore.QObject.connect(self.ui.btnCustomizeHeadings, QtCore.SIGNAL("clicked()"), self.customizeHeadings)
		QtCore.QObject.connect(self.ui.btnCreateProfile, QtCore.SIGNAL("clicked()"), self.createProfile)
		QtCore.QObject.connect(self.ui.btnCancel, QtCore.SIGNAL("clicked()"), self.accept)
		
		self.headings = []
		self.changeProfileType()
		
		self.selectedFile = ''
			 
	def changeProfileType(self):
		self.headings = []
		if self.ui.cboProfileType.currentText() == 'MG-RAST functional profile':		
			self.headings.append('Level 1')
			self.headings.append('Level 2')
			self.headings.append('Level 3')
			self.headings.append('Function')
			self.headings.append('')
			self.headings.append('')
			self.headings.append('')
			self.headings.append('')
		elif self.ui.cboProfileType.currentText() == 'MG-RAST organism profile':	
			self.headings.append('Domain')
			self.headings.append('Phylum')
			self.headings.append('Class')
			self.headings.append('Order')
			self.headings.append('Family')
			self.headings.append('Genus')
			self.headings.append('Species')
			self.headings.append('Strain')

		
	def loadProfiles(self):
		self.selectedFile = QtGui.QFileDialog.getOpenFileName(self, 'Load profile', '', 'MG-RAST profile (*.tsv);;All files (*.*)')

		if self.selectedFile != '':
			self.ui.btnCustomizeHeadings.setEnabled(True)
			self.ui.btnCreateProfile.setEnabled(True)
				
	def customizeHeadings(self):
		customizeHeadingsDlg = CustomizeHeadingsDlg(self) 
		
		if self.ui.cboProfileType.currentText() == 'MG-RAST functional profile':		
			customizeHeadingsDlg.ui.txtInfo.setText('MG-RAST functional profiles consist of four hierarchical levels.')				
		elif self.ui.cboProfileType.currentText() == 'MG-RAST organism profile':	
			customizeHeadingsDlg.ui.txtInfo.setText('MG-RAST organism profiles consist of eight hierarchical levels.')
	 
		customizeHeadingsDlg.ui.txtLevel1.setText(self.headings[0])
		customizeHeadingsDlg.ui.txtLevel2.setText(self.headings[1])
		customizeHeadingsDlg.ui.txtLevel3.setText(self.headings[2])
		customizeHeadingsDlg.ui.txtLevel4.setText(self.headings[3])
		customizeHeadingsDlg.ui.txtLevel5.setText(self.headings[4])
		customizeHeadingsDlg.ui.txtLevel6.setText(self.headings[5])
		customizeHeadingsDlg.ui.txtLevel7.setText(self.headings[6])
		customizeHeadingsDlg.ui.txtLevel8.setText(self.headings[7])
					 
		if customizeHeadingsDlg.exec_() == QtGui.QDialog.Accepted:
			self.headings[0] = customizeHeadingsDlg.ui.txtLevel1.text()
			self.headings[1] = customizeHeadingsDlg.ui.txtLevel2.text()
			self.headings[2] = customizeHeadingsDlg.ui.txtLevel3.text()
			self.headings[3] = customizeHeadingsDlg.ui.txtLevel4.text()
			self.headings[4] = customizeHeadingsDlg.ui.txtLevel5.text()
			self.headings[5] = customizeHeadingsDlg.ui.txtLevel6.text()
			self.headings[6] = customizeHeadingsDlg.ui.txtLevel7.text()
			self.headings[7] = customizeHeadingsDlg.ui.txtLevel8.text()
	
	def createProfile(self):
		# get filename to save STAMP profile to
		stampFilename = QtGui.QFileDialog.getSaveFileName(self, 'Save STAMP profile...', '',
													'STAMP profile file(*.spf);;All files(*.*)')
		if stampFilename == '':
			return
		
		# set profile specific parsing information
		if self.ui.cboProfileType.currentText() == 'MG-RAST functional profile':		
			splitCh = '\t'
			hierarchyStartIndex = 1
			dataIndex = 5
		elif self.ui.cboProfileType.currentText() == 'MG-RAST organism profile':	
			splitCh = '\t'
			hierarchyStartIndex = 2
			dataIndex = 10

		# get profile information from file
		fin = open(self.selectedFile, 'U')
		data = map(string.strip, fin.readlines())
		fin.close()
		
		# determine samples in profile
		sampleNames = []
		for i in xrange(1, len(data)):
			sampleId = data[i].split(splitCh)[0]
			if sampleId not in sampleNames:
				sampleNames.append(sampleId)

		# add profile info
		profileDict = {} 
		duplicateDict = {}
		for i in xrange(1, len(data)):
			if data[i] == "":
					continue	# skip blank lines
			
			lineSplit = data[i].split(splitCh)
			if len(lineSplit) <= dataIndex:
					QtGui.QMessageBox.information(self, 'Unrecognized file format', 'Your file does not appear to be a ' + self.ui.cboProfileType.currentText() + '.')
					return
			
			count = int(lineSplit[dataIndex])
			hierarchy = lineSplit[hierarchyStartIndex:dataIndex]
			
			sampleId = lineSplit[0]
			profileIndex = sampleNames.index(sampleId)
			

			row = profileDict.get(hierarchy[-1], None)
			if row == None:
				row = ProfileRow()
				row.countData = [0] * len(sampleNames)
				row.hierarchy = hierarchy
				profileDict[hierarchy[-1]] = row
				duplicateDict[hierarchy[-1]] = [hierarchy[-2]]
			else:
				if hierarchy[-2] not in duplicateDict[hierarchy[-1]]:
					duplicateDict[hierarchy[-1]].append(hierarchy[-2])

			index = duplicateDict[hierarchy[-1]].index(hierarchy[-2])
			if index == 0:
				row.countData[profileIndex] += count
			else:
				hierarchy[-1] = hierarchy[-1] + ' - ' + str(index)
				newRow = profileDict.get(hierarchy[-1], None)
				if newRow == None:
					newRow = ProfileRow()
					newRow.countData = [0] * len(sampleNames)
					newRow.hierarchy = hierarchy
					profileDict[hierarchy[-1]] = newRow
				newRow.countData[profileIndex] += count

		# write out STAMP profile
		try:
			fout = open(stampFilename, 'w')
		except IOError:
			QtGui.QMessageBox.information(self, 'Failed to save STAMP profile', 'Write permission for file denied.', QtGui.QMessageBox.Ok)
			return

		fout.write(self.headings[0])
		for heading in self.headings[1:(dataIndex-hierarchyStartIndex)]:
			fout.write('\t' + heading)
			
		for sampleName in sampleNames:
			fout.write('\t' + sampleName)
		fout.write('\n')
			
		for key in profileDict.keys():
			row = profileDict[key]
			for h in row.hierarchy:
				fout.write(h + '\t')
			
			fout.write(str(row.countData[0]))
			for c in row.countData[1:]:
				fout.write('\t' + str(c))
			fout.write('\n')
			
		fout.close()
				
		self.accept()

	def centerWindow(self):
		screen = QtGui.QDesktopWidget().screenGeometry()
		size =	self.geometry()
		self.move((screen.width()-size.width())/2, (screen.height()-size.height())/2)
