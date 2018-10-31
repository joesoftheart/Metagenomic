# Make the STAMP.app by default.
default: package

# Declare the following as targets (in case there is also a file of the same name, this avoids confusion).
.PHONY: force clean package

# Any target that uses "force" as a dependancy is forced to execute; i.e. make will never say the target is "up-to-date".
force: ;

STAMP.app: force
	
	@#‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾#
	@#   Remove the old .app   #
	@#_________________________#
	
	@echo "#‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾#\n#   Remove the old .app   #\n#_________________________#"
	rm -rf STAMP.app
	
	@#‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾#
	@#   Create the .app   #
	@#_____________________#
	
	@echo "\n#‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾#\n#   Create the .app   #\n#_____________________#"
	mkdir -p STAMP.app
	mkdir -p STAMP.app/Contents
	@echo -n 'APPL????' > STAMP.app/Contents/PkgInfo
	
	@#‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾#
	@#   Resources   #
	@#_______________#
	
	@echo "\n#‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾#\n#   Resources   #\n#_______________#"
	mkdir -p STAMP.app/Contents/Resources
	if [ ! -d ../library3rd ]; then if [ -a ../library3rd.tar.gz ]; then tar -xzf ../library3rd.tar.gz -C ../; fi; fi
	cp -RpP ../library3rd/Resources/* STAMP.app/Contents/Resources
	echo '#!/bin/bash\n"$${0:0:($$(($${#0}-5)))}../Frameworks/Python.framework/Versions/2.6/Resources/Python.app/Contents/MacOS/Python" "$${0:0:($$(($${#0}-5)))}../Resources/STAMP.py" --macapp &' > STAMP.app/Contents/Resources/STAMP
	chmod a+rx STAMP.app/Contents/Resources/STAMP
	# STAMP Resources:
	cp -RpP ../../../data STAMP.app/Contents/Resources/
	cp -RpP ../../../GUI STAMP.app/Contents/Resources/
	cp -RpP ../../../icons STAMP.app/Contents/Resources/
	cp -RpP ../../../metagenomics STAMP.app/Contents/Resources/
	cp -RpP ../../../plugins STAMP.app/Contents/Resources/
	cp ../../../mainUI.py STAMP.app/Contents/Resources/
	cp ../../../main.ui STAMP.app/Contents/Resources/
	cp ../../../commandLine.py STAMP.app/Contents/Resources/
	cp ../../../STAMP.py STAMP.app/Contents/Resources/
	cp ../../../STAMP_test.py STAMP.app/Contents/Resources/
	cp ../../../license.txt STAMP.app/Contents/Resources/
	mkdir -p STAMP.app/Contents/Resources/English.lproj
	
	@#‾‾‾‾‾‾‾‾‾‾‾‾‾#
	@#   Library   #
	@#_____________#
	
	@echo "\n#‾‾‾‾‾‾‾‾‾‾‾‾‾#\n#   Library   #\n#_____________#"
	mkdir -p STAMP.app/Contents/Library
	if [ ! -d ../library3rd ]; then if [ -a ../library3rd.tar.gz ]; then tar -xzf ../library3rd.tar.gz -C ../; fi; fi
	cp -RpP ../library3rd/Library/* STAMP.app/Contents/Library/
	
	@#‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾#
	@#   Frameworks   #
	@#________________#
	
	@echo "\n#‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾#\n#   Frameworks   #\n#________________#"
	mkdir -p STAMP.app/Contents/Frameworks
	if [ ! -d ../library3rd ]; then if [ -a ../library3rd.tar.gz ]; then tar -xzf ../library3rd.tar.gz -C ../; fi; fi
	cp -RpP ../library3rd/Frameworks/* STAMP.app/Contents/Frameworks/
	
	@#‾‾‾‾‾‾‾‾‾‾‾#
	@#   MacOS   #
	@#___________#
	
	@echo "\n#‾‾‾‾‾‾‾‾‾‾‾#\n#   MacOS   #\n#___________#"
	mkdir -p STAMP.app/Contents/MacOS
	touch STAMP.app/Contents/MacOS/STAMP
	echo '#!/bin/bash\nopen "$${0:0:($$(($${#0}-5)))}../Resources/STAMP"' > STAMP.app/Contents/MacOS/STAMP
	chmod a+rx STAMP.app/Contents/MacOS/STAMP
	
	@echo "\nFinished building STAMP.app!\n"

#‾‾‾‾‾‾‾‾‾‾‾#
#   Clean   #
#___________#

clean:
	@echo "\nCleaning...\n"
	rm -rf STAMP.app
	@echo "\n...done.\n"

#‾‾‾‾‾‾‾‾‾‾‾‾‾#
#   Package   #
#_____________#

package: STAMP.app
	zip -rqy9 STAMP.zip STAMP.app ../../../examples ../../../license.txt
	#rm -rf STAMP.app
	@echo "\nSTAMP.zip package ready!\n"
