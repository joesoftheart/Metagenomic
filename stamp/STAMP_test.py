'''
Unit tests for STAMP.

Created on Jan 8, 2010

@author: Donovan Parks
'''

import unittest

# test tables (positive samples 1, positive samples 2, total samples 1, total samples 2)
table1 = [10, 8, 30, 40]
table2 = [4000, 5000, 500000, 1000000]

# preferences for statistical tests
preferences = {}
preferences['Pseudocount'] = 0.5

class VerifyStatisticalTests(unittest.TestCase):      
  def testBarnard(self):
    """Verify computation of Barnard's exact test"""
    from plugins.statisticalTests.Barnard import Barnard
    barnard = Barnard(preferences)

    # Ground truth obtained from StatXact v8.0.0
    oneSided, twoSided = barnard.hypothesisTest(table1[0], table1[1], table1[2], table1[3])
    self.assertEqual(oneSided, float('inf'))
    self.assertAlmostEqual(twoSided, 0.224594642210276)
    
  def testChiSquare(self):
    """Verify computation of Chi-square test"""
    from plugins.statisticalTests.ChiSquare import ChiSquare
    chiSquare = ChiSquare(preferences) 
    
    # Ground truth obtained from R version 2.10    
    oneSided, twoSided = chiSquare.hypothesisTest(table1[0], table1[1], table1[2], table1[3])   
    self.assertEqual(oneSided, float('inf'))
    self.assertAlmostEqual(twoSided, 0.206550401252)
    
    oneSided, twoSided = chiSquare.hypothesisTest(table2[0], table2[1], table2[2], table2[3])   
    self.assertEqual(oneSided, float('inf'))
    self.assertAlmostEqual(twoSided, 2.220446049e-16)
    
  def testChiSquareYates(self):
    """Verify computation of Chi-square test with Yates' continuity correction"""
    from plugins.statisticalTests.ChiSquareYates import ChiSquareYates
    chiSquareYates = ChiSquareYates(preferences)
    
    # Ground truth obtained from R version 2.10    
    oneSided, twoSided = chiSquareYates.hypothesisTest(table1[0], table1[1], table1[2], table1[3])   
    self.assertEqual(oneSided, float('inf'))
    self.assertAlmostEqual(twoSided, 0.323739196466)
    
    oneSided, twoSided = chiSquareYates.hypothesisTest(table2[0], table2[1], table2[2], table2[3])   
    self.assertEqual(oneSided, float('inf'))
    self.assertAlmostEqual(twoSided, 2.220446049e-16)
    
  def testDiffBetweenProp(self):
    """Verify computation of Difference between proportions test"""
    from plugins.statisticalTests.DiffBetweenProp import DiffBetweenProp
    diffBetweenProp = DiffBetweenProp(preferences)
    
    # Ground truth obtained from R version 2.10    
    oneSided, twoSided = diffBetweenProp.hypothesisTest(table1[0], table1[1], table1[2], table1[3])   
    self.assertAlmostEqual(oneSided, 0.103275200626)
    self.assertAlmostEqual(twoSided, 0.206550401252)
    
    oneSided, twoSided = diffBetweenProp.hypothesisTest(table2[0], table2[1], table2[2], table2[3])   
    self.assertAlmostEqual(oneSided, 2.220446049e-16)
    self.assertAlmostEqual(twoSided, 2.220446049e-16)

  def testFishers(self):
    """Verify computation of Fisher's exact test (minimum-likelihood approach)"""
    from plugins.statisticalTests.Fishers import Fishers
    fishers = Fishers(preferences)
    
    # Ground truth obtained from R version 2.10    
    oneSided, twoSided = fishers.hypothesisTest(table1[0], table1[1], table1[2], table1[3])   
    self.assertAlmostEqual(oneSided, 0.16187126209690825)
    self.assertAlmostEqual(twoSided, 0.2715543327789185)
    
    oneSided, twoSided = fishers.hypothesisTest(table2[0], table2[1], table2[2], table2[3])   
    self.assertAlmostEqual(oneSided, 2.220446049e-16)
    self.assertAlmostEqual(twoSided, 2.220446049e-16)
    
  def testGTest(self):
    """Verify computation of G-test"""
    from plugins.statisticalTests.GTest import GTest
    gTest = GTest(preferences)
    
    # Ground truth obtained from Peter L. Hurd's R script (http://www.psych.ualberta.ca/~phurd/cruft/g.test.r)  
    oneSided, twoSided = gTest.hypothesisTest(table1[0], table1[1], table1[2], table1[3])   
    self.assertEqual(oneSided, float('inf'))
    self.assertAlmostEqual(twoSided, 0.208248664458)
    
    oneSided, twoSided = gTest.hypothesisTest(table2[0], table2[1], table2[2], table2[3])   
    self.assertEqual(oneSided, float('inf'))
    self.assertAlmostEqual(twoSided, 2.220446049e-16)
    
  def testGTestYates(self):
    """Verify computation of G-test with Yates' continuity correction"""
    from plugins.statisticalTests.GTestYates import GTestYates
    gTestYates = GTestYates(preferences)
    
    # Ground truth obtained from Peter L. Hurd's R script (http://www.psych.ualberta.ca/~phurd/cruft/g.test.r)  
    oneSided, twoSided = gTestYates.hypothesisTest(table1[0], table1[1], table1[2], table1[3])   
    self.assertEqual(oneSided, float('inf'))
    self.assertAlmostEqual(twoSided, 0.325502240010)
    
    oneSided, twoSided = gTestYates.hypothesisTest(table2[0], table2[1], table2[2], table2[3])   
    self.assertEqual(oneSided, float('inf'))
    self.assertAlmostEqual(twoSided, 2.220446049e-16)
    
  def testHypergeometric(self):
    """Verify computation of Hypergeometric test (Fisher's exact test with p-value doubling approach)"""
    from plugins.statisticalTests.Hypergeometric import Hypergeometric
    hypergeometric = Hypergeometric(preferences)
    
    # Ground truth obtained using the phyper() and dyper() function in R version 2.10   
    oneSided, twoSided = hypergeometric.hypothesisTest(table1[0], table1[1], table1[2], table1[3])   
    self.assertAlmostEqual(oneSided, 0.161871262097)
    self.assertAlmostEqual(twoSided, 2 * 0.161871262097)
    
    oneSided, twoSided = hypergeometric.hypothesisTest(table2[0], table2[1], table2[2], table2[3])   
    self.assertAlmostEqual(oneSided, 2.220446049e-16)
    self.assertAlmostEqual(twoSided, 2.220446049e-16)
    
class VerifyEffectSizeFilters(unittest.TestCase):
  def testDiffBetweenProp(self):
    """Verify computation of Difference between proportions effect size filter"""
    from plugins.effectSizeFilters.DiffBetweenProp import DiffBetweenProp
    diffBetweenProp = DiffBetweenProp(preferences)
    
    # Ground truth calculated by hand
    value = diffBetweenProp.run(table1[0], table1[1], table1[2], table1[3])
    self.assertAlmostEqual(value, 13.333333333)
    
    value = diffBetweenProp.run(table2[0], table2[1], table2[2], table2[3])
    self.assertAlmostEqual(value, 0.3)
    
  def testOddsRatio(self):
    """Verify computation of Odds ratio effect size filter"""
    from plugins.effectSizeFilters.OddsRatio import OddsRatio
    oddsRatio = OddsRatio(preferences)
    
    # Ground truth calculated by hand
    value = oddsRatio.run(table1[0], table1[1], table1[2], table1[3])
    self.assertAlmostEqual(value, 2.0)
    
    value = oddsRatio.run(table2[0], table2[1], table2[2], table2[3])
    self.assertAlmostEqual(value, 1.60483870968)
    
  def testRatioProportions(self):
    """Verify computation of Difference between proportions effect size filter"""
    from plugins.effectSizeFilters.RatioProportions import RatioProportions
    ratioProportions = RatioProportions(preferences)
    
    # Ground truth calculated by hand
    value = ratioProportions.run(table1[0], table1[1], table1[2], table1[3])
    self.assertAlmostEqual(value, 1.66666666666666)
    
    value = ratioProportions.run(table2[0], table2[1], table2[2], table2[3])
    self.assertAlmostEqual(value, 1.6)
    
class VerifyConfidenceIntervalMethods(unittest.TestCase):
  def testDiffBetweenPropAsymptotic(self):
    """Verify computation of Difference between proportions asymptotic CI method"""
    from plugins.confidenceIntervalMethods.DiffBetweenPropAsymptotic import DiffBetweenPropAsymptotic
    diffBetweenPropAsymptotic = DiffBetweenPropAsymptotic(preferences)
    
    lowerCI, upperCI, effectSize = diffBetweenPropAsymptotic.run(table1[0], table1[1], table1[2], table1[3], 0.95)
    self.assertAlmostEqual(lowerCI, -7.60015319099813)
    self.assertAlmostEqual(upperCI, 34.2668198576648)
    self.assertAlmostEqual(effectSize, 13.333333333)
        
    lowerCI, upperCI, effectSize= diffBetweenPropAsymptotic.run(table2[0], table2[1], table2[2], table2[3], 0.95)
    self.assertAlmostEqual(lowerCI, 0.271701079166334)
    self.assertAlmostEqual(upperCI, 0.328298920833666)
    self.assertAlmostEqual(effectSize, 0.3)
    
  def testDiffBetweenPropAsymptoticCC(self):
    """Verify computation of Difference between proportions asymptotic CI method with continuity correction"""
    from plugins.confidenceIntervalMethods.DiffBetweenPropAsymptoticCC import DiffBetweenPropAsymptoticCC
    diffBetweenPropAsymptoticCC = DiffBetweenPropAsymptoticCC(preferences)
    
    lowerCI, upperCI, effectSize = diffBetweenPropAsymptoticCC.run(table1[0], table1[1], table1[2], table1[3], 0.95)    
    self.assertAlmostEqual(lowerCI, -13.3167148125733)
    self.assertAlmostEqual(upperCI, 39.98338147924)
    self.assertAlmostEqual(effectSize, 13.333333333)
        
    lowerCI, upperCI, effectSize= diffBetweenPropAsymptoticCC.run(table2[0], table2[1], table2[2], table2[3], 0.95)
    self.assertAlmostEqual(lowerCI, 0.271407084568653)
    self.assertAlmostEqual(upperCI, 0.328592915431347)
    self.assertAlmostEqual(effectSize, 0.3)
    
  def testNewcombeWilson(self):
    """Verify computation of Newcombe-Wilson CI method"""
    from plugins.confidenceIntervalMethods.NewcombeWilson import NewcombeWilson
    newcombeWilson = NewcombeWilson(preferences)
    
    lowerCI, upperCI, effectSize = newcombeWilson.run(table1[0], table1[1], table1[2], table1[3], 0.95)    
    self.assertAlmostEqual(lowerCI, -7.07911677674112)
    self.assertAlmostEqual(upperCI, 33.5862638376494)
    self.assertAlmostEqual(effectSize, 13.333333333)
        
    lowerCI, upperCI, effectSize= newcombeWilson.run(table2[0], table2[1], table2[2], table2[3], 0.95)
    self.assertAlmostEqual(lowerCI, 0.271932757939523)
    self.assertAlmostEqual(upperCI, 0.328541077116921)
    self.assertAlmostEqual(effectSize, 0.3)
    
  def testOddsRatio(self):
    """Verify computation of Odds ratio CI method"""
    from plugins.confidenceIntervalMethods.OddsRatio import OddsRatio
    oddsRatio = OddsRatio(preferences)
    
    # Ground truth calculated by hand
    lowerCI, upperCI, effectSize = oddsRatio.run(table1[0], table1[1], table1[2], table1[3], 0.95)  
    self.assertAlmostEqual(lowerCI, 0.676046021596)
    self.assertAlmostEqual(upperCI, 5.91675695474)
    self.assertAlmostEqual(effectSize, 2.0)
         
    lowerCI, upperCI, effectSize = oddsRatio.run(table2[0], table2[1], table2[2], table2[3], 0.95)
    self.assertAlmostEqual(lowerCI, 1.53926774059)
    self.assertAlmostEqual(upperCI, 1.6732029238)
    self.assertAlmostEqual(effectSize, 1.60483870968)
    
  def testRatioProportions(self):
    """Verify computation of Ratio of proportions CI method"""
    from plugins.confidenceIntervalMethods.RatioProportions import RatioProportions
    ratioProportions = RatioProportions(preferences)
    
    # Ground truth calculated by hand
    lowerCI, upperCI, effectSize = ratioProportions.run(table1[0], table1[1], table1[2], table1[3], 0.95)  
    self.assertAlmostEqual(lowerCI, 0.748767825898)
    self.assertAlmostEqual(upperCI, 3.70979852726)
    self.assertAlmostEqual(effectSize, 1.66666666666666)
        
    lowerCI, upperCI, effectSize= ratioProportions.run(table2[0], table2[1], table2[2], table2[3], 0.95)
    self.assertAlmostEqual(lowerCI, 1.53505365781)
    self.assertAlmostEqual(upperCI, 1.6676941467)
    self.assertAlmostEqual(effectSize, 1.6)
    
class VerifyMultipleComparisonCorrectionMethods(unittest.TestCase):  
  pValues = [1e-6, 1e-5, 1e-4, 1e-3, 1e-2, 1e-1]
  
  def testBenjaminiHochbergFDR(self):
    """Verify computation of Bejamini-Hochberg FDR method"""
    from plugins.multipleComparisonCorrections.BenjaminiHochbergFDR import BenjaminiHochbergFDR
    benjaminiHochbergFDR = BenjaminiHochbergFDR(preferences)
    
    # Ground truth calculated explicitly
    qValues = benjaminiHochbergFDR.correct(list(self.pValues), 0.05)
    modifier = 1
    for i in xrange(0, len(self.pValues)):
      self.assertAlmostEqual(qValues[i], self.pValues[i]*len(self.pValues) / modifier)
      modifier += 1
      
  def testBonferroni(self):
    """Verify computation of Bonferroni method"""
    from plugins.multipleComparisonCorrections.Bonferroni import Bonferroni
    bonferroni = Bonferroni(preferences)
    
    # Ground truth calculated explicitly
    correctedValues = bonferroni.correct(list(self.pValues), 0.05)
    modifier = 1
    for i in xrange(0, len(self.pValues)):
      self.assertAlmostEqual(correctedValues[i], self.pValues[i]*len(self.pValues))
      
  def testHolmBonferroni(self):
    """Verify computation of Holm-Bonferroni method"""
    from plugins.multipleComparisonCorrections.HolmBonferroni import HolmBonferroni
    holmBonferroni = HolmBonferroni(preferences)
    
    # Ground truth calculated by hand
    correctedValues = holmBonferroni.correct(list(self.pValues), 0.05)
    self.assertAlmostEqual(correctedValues[0], self.pValues[0])
    self.assertAlmostEqual(correctedValues[1], self.pValues[1])
    self.assertAlmostEqual(correctedValues[2], self.pValues[2])
    self.assertAlmostEqual(correctedValues[3], self.pValues[3])
    self.assertAlmostEqual(correctedValues[4], self.pValues[4])
    self.assertEqual(correctedValues[5], float('inf'))
    
  def testNoCorrection(self):
    """Verify computation of No multiple comparison correction method"""
    from plugins.multipleComparisonCorrections.NoCorrection import NoCorrection
    noCorrection = NoCorrection(preferences)
    
    # Ground truth calculated explicitly
    correctedValues = noCorrection.correct(list(self.pValues), 0.05)
    for i in xrange(0, len(self.pValues)):
      self.assertAlmostEqual(correctedValues[i], self.pValues[i])
      
  def testSidak(self):
    """Verify computation of Sidak method"""
    from plugins.multipleComparisonCorrections.Sidak import Sidak
    sidak = Sidak(preferences)
    
    # Ground truth calculated explicitly
    correctedValues = sidak.correct(list(self.pValues), 0.05)
    for i in xrange(0, len(self.pValues)):
      self.assertAlmostEqual(correctedValues[i], 1.0 - (1.0 - self.pValues[i])**len(self.pValues))
      
  def testStoreyFDR(self):
    """Verify computation of Storey FDR method"""
   
    # This method is based on a bootstrapping approach and as such does not always produce
    # identical results. It has been tested against the results given by the R plugin by
    # Alan Dadney and John Storey (http://cran.r-project.org/web/packages/qvalue/)
    pass

if __name__ == "__main__":
  unittest.main()