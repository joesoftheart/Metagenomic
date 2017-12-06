'''
Created on Nov 24, 2009

@author: parks
'''

import imp, os, sys
import os.path

def runningExecutable():
   return (hasattr(sys, "frozen") or # new py2exe
           hasattr(sys, "importers") # old py2exe
           or imp.is_frozen("__main__")) # tools/freeze
   
def getMainDir():
   if runningExecutable():
       return os.path.dirname(sys.executable)
   return sys.path[0]
