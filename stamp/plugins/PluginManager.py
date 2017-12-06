'''
Handle loading plugins

@author: Donovan Parks
'''

import os.path

from metagenomics.DirectoryHelper import runningExecutable

class PluginManager:
  def __init__(self, preferences):
    self.preferences = preferences
    pass
    
  def loadPlugins(self, pluginFolder):
    pluginModulePath = pluginFolder.replace('/', '.')
    
    if runningExecutable():
      pluginFolder = 'library/' + pluginFolder
    
    dict = {}
    for filename in os.listdir(pluginFolder):
      if os.path.isdir(os.path.join (pluginFolder, filename)):
        continue

      extension = filename[filename.rfind('.')+1:len(filename)]  
      if extension == 'py' and filename != '__init__.py':           
        pluginModule = filename[0:filename.rfind('.')]   
        theModule = __import__(pluginModulePath + pluginModule, fromlist='*')
        theClass = getattr(theModule, pluginModule)
        theObject = theClass(self.preferences)       
        dict[theObject.name] = theObject
           
    return dict
  
  def populateComboBox(self, dict, comboBox, defaultPlugin):
    for key in sorted(dict.keys()):
      comboBox.addItem(key)
    comboBox.setCurrentIndex(comboBox.findText(defaultPlugin))
