'''
Command-line interface to STAMP.

Example usage:
 python STAMP.py --f ./examples/EdwardsIronMine.spf --sample1 Red --sample2 Black --statTest "Fisher's exact test"

@author: Donovan Parks
'''

import sys, os
from optparse import OptionParser

from metagenomics.fileIO.StampIO import StampIO
from metagenomics.stats.StatsTests import StatsTests
from metagenomics.TableHelper import SortTableStrCol

from plugins.PluginManager import PluginManager

from metagenomics.DirectoryHelper import getMainDir

class CommandLineParser():
	def __init__(self, preferences):
		self.preferences = preferences

		# load statistical technique plugins
		pluginManager = PluginManager(self.preferences)
		self.effectSizeDict = pluginManager.loadPlugins('plugins/effectSizeFilters/')
		self.statTestDict = pluginManager.loadPlugins('plugins/statisticalTests/')
		self.multCompDict = pluginManager.loadPlugins('plugins/multipleComparisonCorrections/')
		self.confIntervMethodDict = pluginManager.loadPlugins('plugins/confidenceIntervalMethods/')

	def run(self):
		# *** Parse command line
		parser = OptionParser(usage='%prog [--file] [--sample1] [--sample2]', version='STAMP command line interface v1.0')

		# profile properties
		parser.add_option('-f', '--file', action='store', type='string', dest='file', default='', help='STAMP profile file to process')
		parser.add_option('-1', '--sample1', action='store', type='string', dest='sampleName1', default='', help='Name of sample 1')
		parser.add_option('-2', '--sample2', action='store', type='string', dest='sampleName2', default='', help='Name of sample 2')
		parser.add_option('-a', '--profLevel', action='store', type='string', dest='profLevel', default='[Lowest level in hierarchy]', help='Hierarchical level to perform statistical analysis upon')
		parser.add_option('-b', '--parentLevel', action='store', type='string', dest='parentLevel', default='Entire sample', help='Parental level used to calculate relative proportions')

		# statistical testing properties
		parser.add_option('-s', '--statTest', action='store', type='string', dest='statTest', default='Fisher\'s exact test', help='Statistical hypothesis test to use')
		parser.add_option('-q', '--testType', action='store', type='string', dest='testType', default='Two sided', help='Perform either a one or two-sided test')
		parser.add_option('-c', '--CI', action='store', type='string', dest='ciMethod', default='DP: Newcombe-Wilson', help='Confidence interval method to use')
		parser.add_option('-n', '--coverage', action='store', type='float', dest='coverage', default='0.95', help='Coverage of confidence interval')
		parser.add_option('-m', '--multComp', action='store', type='string', dest='multComp', default='No correction', help='Multiple comparison method to use')

		# filtering properties
		parser.add_option('-p', '--pValueFilter', action='store', type='float', dest='pValueFilter', default=0.05, help='Filter out features with a p-value greater than this value')

		parser.add_option('-y', '--seqFilter', action='store', type='string', dest='seqFilter', default='', help='Filter to apply to counts in profile level')
		parser.add_option('-u', '--sample1Filter', action='store', type='int', dest='sample1Filter', default=0, help='Filter criteria for sample 1')
		parser.add_option('-i', '--sample2Filter', action='store', type='int', dest='sample2Filter', default=0, help='Filter criteria for sample 2')

		parser.add_option('-j', '--parentSeqFilter', action='store', type='string', dest='parentSeqFilter', default='', help='Filter to apply to counts in parent level')
		parser.add_option('-k', '--parentSample1Filter', action='store', type='int', dest='parentSample1Filter', default=0, help='Filter to apply to counts in parent level')
		parser.add_option('-l', '--parentSample2Filter', action='store', type='int', dest='parentSample2Filter', default=0, help='Filter to apply to counts in parent level')

		parser.add_option('-e', '--effectSizeMeasure1', action='store', type='string', dest='effectSizeMeasure1', default='', help='Effect size measure to use')
		parser.add_option('-r', '--minEffectSize1', action='store', type='float', dest='minEffectSize1', default=0, help='Minimum required effect size')

		parser.add_option('-z', '--effectSizeMeasure2', action='store', type='string', dest='effectSizeMeasure2', default='', help='Effect size measure to use')
		parser.add_option('-x', '--minEffectSize2', action='store', type='float', dest='minEffectSize2', default=0, help='Minimum required effect size')

		parser.add_option('-w', '--effectSizeOperator', action='store', type='int', dest='effectSizeOperator', default=0, help='Logical operator to apply to effect size filters (0 - OR, 1 - AND)')

		# output properties
		parser.add_option('-t', '--outputTable', action='store', type='string', dest='tableFile', default='results.tsv', help='Filename for table')

		# misc. properties
		parser.add_option('-v', '--verbose', action='store', type='int', dest='bVerbose', default=1, help='Print progress information (1) or suppress all output (0)')

		(options, args) = parser.parse_args()

		if options.file == '':
			parser.error('An input file must be specified (--file) ')
			sys.exit()

		if options.sampleName1 == '' or options.sampleName2 == '':
			parser.error('Sample names must be specified (--sample1 and --sample2) ')
			sys.exit()

		if not (options.testType == '' or options.testType == 'Two sided' or options.testType == 'One sided'):
			parser.error('Valid values for --testType are \'Two sided\' and \'One sided\'.')
			sys.exit()

		self.bVerbose = (options.bVerbose != 0)

		# *** Load profile file and create desired profile
		if self.bVerbose:
			print 'Creating desired profile	.'
		profile = self.createProfile(options.file, options.sampleName1, options.sampleName2, options.parentLevel, options.profLevel)

		# *** Run statistical test
		if self.bVerbose:
			print 'Performing statistical analysis	.'

		statsTestResults = self.runStatTest(profile, options.statTest, options.testType, options.ciMethod, options.coverage, options.multComp)

		# *** Filter features
		if self.bVerbose:
			print 'Filtering features	.'

		self.filterFeatures(statsTestResults, options.pValueFilter, options.seqFilter, options.sample1Filter, options.sample2Filter,
														options.parentSeqFilter, options.parentSample1Filter, options.parentSample2Filter,
														options.effectSizeMeasure1, options.minEffectSize1, options.effectSizeOperator,
														options.effectSizeMeasure2, options.minEffectSize2)

		if self.bVerbose:
			print '	Active features: ' + str(len(statsTestResults.getActiveFeatures()))

		# *** Create output table
		if self.bVerbose:
			print 'Saving results to ' + options.tableFile + '	.'

		self.saveSummaryTable(options.tableFile, statsTestResults, options.sampleName1, options.sampleName2, options.coverage)

		if self.bVerbose:
			print 'Done.'

	def createProfile(self, file, sampleName1, sampleName2, parentLevel, profLevel):
		# load profile tree from file
		try:
			stampIO = StampIO(self.preferences)
			profileTree, errMsg = stampIO.read(file)

			if errMsg != None:
				print errMsg
				sys.exit()

		except:
			print 'Unknown error while reading input file'
			sys.exit()

		# setup desired level in hierarchy
		if profLevel == '[Lowest level in hierarchy]':
			profLevel = profileTree.hierarchyHeadings[-1]

		# create profile for desired samples at the desired hierarchical levels
		errMsg = ''
		if sampleName1 not in profileTree.sampleNames:
			errMsg = 'Sample ' + '\'' + sampleName1 + '\'' + ' could not be found in the input file.'
		elif sampleName2 not in profileTree.sampleNames:
			errMsg = 'Sample ' + '\'' + sampleName2 + '\'' + ' could not be found in the input file.'
		elif parentLevel != 'Entire sample' and parentLevel not in profileTree.hierarchyHeadings:
			errMsg = 'Hierarchical level ' + '\'' + parentLevel + '\'' + ' could not be found in the input file.'
		elif profLevel not in profileTree.hierarchyHeadings:
			errMsg = 'Hierarchical level ' + '\'' + profLevel + '\'' + ' could not be found in the input file.'

		depthTest = list(['Entire sample'] + profileTree.hierarchyHeadings)
		if depthTest.index(parentLevel) >= depthTest.index(profLevel):
			errMsg = 'Specified parent level is at the same level or lower than the specified profile level.'

		if errMsg != '':
			print errMsg
			sys.exit()

		profile = profileTree.createProfile(sampleName1, sampleName2, parentLevel, profLevel)

		return profile

	def filterFeatures(self, statsTestResults, signLevelFilter, seqFilter, sample1Filter, sample2Filter,
						parentSeqFilter, parentSample1Filter, parentSample2Filter,
						effectSizeMeasure1, minEffectSize1, effectSizeOperator,
						effectSizeMeasure2, minEffectSize2):
		# perform filtering
		if signLevelFilter >= 1.0:
			signLevelFilter = None

		# perform filtering
		if seqFilter == '':
			seqFilter = None
			sample1Filter = None
			sample2Filter = None

		if parentSeqFilter == '':
			parentSeqFilter = None
			parentSample1Filter = None
			parentSample2Filter = None

		# effect size filters
		if effectSizeMeasure1 != '':
			effectSizeMeasure1 = self.effectSizeDict[effectSizeMeasure1]
		else:
			effectSizeMeasure1 = None
			minEffectSize1 = None

		if effectSizeMeasure2 != '':
			effectSizeMeasure2 = self.effectSizeDict[effectSizeMeasure2]
		else:
			effectSizeMeasure2 = None
			minEffectSize2 = None

		if effectSizeOperator == 0:
			effectSizeOperator = 'OR'
		else:
			effectSizeOperator = 'AND'

		statsTestResults.filterFeatures(signLevelFilter, seqFilter, sample1Filter, sample2Filter,
										parentSeqFilter, parentSample1Filter, parentSample2Filter,
										effectSizeMeasure1, minEffectSize1, effectSizeOperator,
										effectSizeMeasure2, minEffectSize2)

	def runStatTest(self, profile, statTest, testType, ciMethod, coverage, multComp):
		# run significance test
		test =	self.statTestDict[statTest]
		multCompMethod = self.multCompDict[multComp]
		confIntervMethod = self.confIntervMethodDict[ciMethod]

		statsTest = StatsTests()
		progressIndicator = 'Verbose'
		if self.bVerbose == False:
			progressIndicator = None
		statsTest.run(test, testType, confIntervMethod, coverage, profile, progressIndicator)
		statsTest.results.performMultCompCorrection(multCompMethod)
		statsTest.results.selectAllFeautres()

		return statsTest.results

	def saveSummaryTable(self, filename, statsTestResults, sampleName1, sampleName2, coverage):
		tableData = statsTestResults.tableData(True)
	
		# update table summarizing statistical results
		oneMinAlphaStr = str((1.0 - coverage))
		tableHeadings = list(statsTestResults.profile.hierarchyHeadings)
		tableHeadings += [sampleName1, sampleName2]
		tableHeadings += ['Parent seq. 1', 'Parent seq. 2']
		tableHeadings += ['Rel. freq. 1 (%)','Rel. freq. 2 (%)']
		tableHeadings += ['p-values','p-values (corrected)']
		tableHeadings += ['Effect size']
		tableHeadings += [str(coverage*100) + '% lower CI']
		tableHeadings += [str(coverage*100) + '% upper CI']
		tableHeadings += ['Power (alpha = ' + oneMinAlphaStr + ')']
		tableHeadings += ['Equal sample size (alpha = ' + oneMinAlphaStr + '; power = 0.80)']

		tableData = SortTableStrCol(tableData, 0)

		fout = open(filename, 'w')
		for heading in tableHeadings:
			fout.write(heading + '\t')
		fout.write('\n')

		for row in tableData:
			for entry in row:
				fout.write(str(entry) + '\t')
			fout.write('\n')

		fout.close()

if __name__ == "__main__":
	# change the current working directory
	os.chdir(getMainDir())

	# initialize preferences
	preferences = {}
	preferences['Pseudocount'] = 0.5
	preferences['Executable directory'] = sys.path[0]

	commandLineParser = CommandLineParser(preferences)
	commandLineParser.run()
	sys.exit()

