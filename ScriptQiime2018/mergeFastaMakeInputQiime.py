
"""I wrote this script for merge all FASTA files in a folder, then
create single file as an QIIME input sequence [valid on both Qiime1 and Qiime2]

Usage: mergeFastaMakeInputQiime.py --inPath ./myFolder/fastaFolder/ --outFasta ./myFolder/inputSequenceQiime.fasta"""

######################################################################################################################
###[0] Load required python modules / create arguments to get data and parameters from a user
######################################################################################################################
"""Import Required Modules"""
from Bio import SeqIO
import argparse
import os
# from importlib import reload
# import sys
# reload(sys)
# sys.setdefaultencoding('utf-8')

"""Create arguments for parsing parameters"""
#Instantiate the parser

parser = argparse.ArgumentParser(description='Description: The script for merge all FASTA files into single qiime input FASTA')

#Add Positional Arguments [required parameters]/No Positional arguments

##Add Optional Arguments [optional parameters]
parser.add_argument('--inPath', type=str, default="./", help="Folder containing fasta file")
parser.add_argument('--outFasta', type=str, default="inputQiime.fasta", help="Output merged Fasta for qiime")

##Get value for each parameters
args = parser.parse_args()

pathInput = args.inPath
outputFile = args.outFasta

"""Summary Input and Output Files"""
print("="*100)
print("SUMMARY INPUT")
print("Input Path:",pathInput)
print("Output merged Fasta:",outputFile)
print("="*100)
######################################################################################################################
###[1] Read files in folder and merge into qiime input
######################################################################################################################
if os.path.exists(pathInput):
    fileList = os.listdir(pathInput)
    fileList.sort()
    saveFile = open(outputFile,"w")
    os.chdir(pathInput)
    
    """Initial sequence order"""
    num = 0

    """Read Fasta File and process"""
    for fileName in fileList:
        #print(fileName)
        openSeq = open(fileName,'r')
        for seqRecord in SeqIO.parse(openSeq,'fasta'):
            header = seqRecord.description
            sequence = str(seqRecord.seq)
            num = num + 1
            write = ">"+fileName.split(".")[0].replace("_","t")+"_"+str(num)+"\n"+sequence+"\n"
            saveFile.write(write)
    saveFile.close()

    """Print Output"""
    print("SUMMARY OUTPUT")
    print("No. input file:",len(fileList))
    print("No. total sequence:",num)
    print("="*100)

else:
    print("ERROR...Your path does not exist.")


