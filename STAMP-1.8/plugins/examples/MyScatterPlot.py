'''
Simple exploratory scatter plot.

@author: Donovan Parks
'''

import sys

from plugins.AbstractStatPlotPlugin import AbstractStatPlotPlugin, TestWindow, ConfigureDialog


class MyScatterPlot(AbstractStatPlotPlugin):
  '''
  Simple exploratory scatter plot.
  '''
  def __init__(self, preferences, parent=None):
    AbstractStatPlotPlugin.__init__(self, preferences, parent)
    self.preferences = preferences
   
    self.name = 'My scatter plot'
    self.figWidth = 6.0
    self.figHeight = 6.0
    
    self.sampleName1 = ''
    self.sampleName2 = ''
    
  def plot(self, statsResults):    
    # Colour of plot elements
    profile1Colour = str(self.preferences['Sample 1 colour'].name())
    profile2Colour = str(self.preferences['Sample 2 colour'].name())
    
    # Set sample names
    if self.sampleName1 == '' and self.sampleName2 == '':
      self.sampleName1 = statsResults.profile.sampleNames[0]
      self.sampleName2 = statsResults.profile.sampleNames[1]
        
    # Get data to plot    
    field1 = statsResults.getColumn('RelFreq1')
    field2 = statsResults.getColumn('RelFreq2')
          
    # Set figure size
    self.fig.clear()
    self.fig.set_size_inches(self.figWidth, self.figHeight)  
    axesScatter = self.fig.add_subplot(111)
    
    # Set visual properties of all points
    colours = []
    for i in xrange(0, len(field1)):
      if field1[i] > field2[i]:
        colours.append(profile1Colour)    
      else:
        colours.append(profile2Colour)
           
    # Create scatter plot
    axesScatter.scatter(field1, field2, c=colours)

    # Update plot
    self.updateGeometry()       
    self.draw()
