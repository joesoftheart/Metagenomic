
from Bio import SeqIO
import sys
import subprocess
import os


dir=sys.argv[1]
outputFile=sys.argv[2]
saveFile = open(outputFile,"w")
os.chdir(dir)
p=subprocess.run(["ls"], stdout=subprocess.PIPE)
fileList=p.stdout.strip().decode('ascii').split('\n')

"""Initial sequence order"""
num = 0

"""Read Fasta File and process"""
for fileName in fileList:
    #print(fileName)
    openSeq = open(fileName,'r')
    for seqRecord in SeqIO.parse(openSeq,'fasta'):
        header = seqRecord.description
        sequence =  str(seqRecord.seq)
        num = num + 1
        write = ">"+fileName.split(".")[0].replace("_","t")+"_"+str(num)+"\n"+sequence+"\n"
            
        saveFile.write(write)
saveFile.close()

# for name in filelist:
#     output=name+'_out'
#     fa=[]
#     with open(name, 'r') as file:  
#         for line in file:  
#             if line[0]=='>':  
#                 line='>'+name+line[1:]  
#             fa.append(line)  
#     with open(output, 'w') as out:  
#         out.writelines(fa) 


