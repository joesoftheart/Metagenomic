from distutils.core import setup
import py2exe
import os
import matplotlib as mpl

plugin_directories = ['','/confidenceIntervalMethods','/effectSizeFilters','/exploratoryPlots','/exploratoryPlots/configGUI',
													'/multipleComparisonCorrections','/statisticalTests','/statPlots','/statPlots/configGUI']
plugin_files = []
for directory in plugin_directories:
	for files in os.listdir("plugins" + directory):
	    f1 = "plugins" + directory + "/" + files
	    if os.path.isfile(f1): # skip directories
	        f2 = "library/plugins" + directory, [f1]
	        plugin_files.append(f2)
			
metagenomic_directories = ['/CI']
metagenomic_files = []
for directory in metagenomic_directories:
	for files in os.listdir("metagenomics/stats" + directory):
	    f1 = "metagenomics/stats" + directory + "/" + files
	    if os.path.isfile(f1): # skip directories
	        f2 = "library/metagenomics/stats" + directory, [f1]
	        metagenomic_files.append(f2)

icon_files = []
for files in os.listdir("icons"):
    f1 = "icons/" + files
    if os.path.isfile(f1): # skip directories
        f2 = "icons", [f1]
        icon_files.append(f2)
        
example_files = []
for files in os.listdir("examples"):
    f1 = "examples/" + files
    if os.path.isfile(f1): # skip directories
        f2 = "examples", [f1]
        example_files.append(f2)
        
data_files = []
for files in os.listdir("data"):
    f1 = "data/" + files
    if os.path.isfile(f1): # skip directories
        f2 = "data", [f1]
        data_files.append(f2)
        
mpl_data_files = mpl.get_py2exe_datafiles()

setup(
	name = "STAMP",
	version = "1.08",
	description = "Statistical analysis of metagenomic profiles",
	author = "Donovan Parks",
	windows=[{"script":"STAMP.py", "icon_resources": [(1, "icons/programIcon.ico")]}],
	console=[{"script":"commandLine.py", "icon_resources": [(1, "icons/programIcon.ico")]}],
	options = 
			{
				"py2exe":
				{
					"unbuffered": True,
					"optimize": 2,
					"skip_archive": True,
					"includes": ["sip", "PyQt4", "sqlite3"],
					"packages": ["matplotlib","pytz","mpmath","scipy","mpl_toolkits"],
					"dll_excludes": ["libgdk_pixbuf-2.0-0.dll","libgdk-win32-2.0-0.dll", "libgobject-2.0-0.dll", "tcl84.dll", "tk84.dll"],
				}
			},
	zipfile = "library/",
	data_files = plugin_files + metagenomic_files + icon_files + example_files + data_files + mpl_data_files,
)
