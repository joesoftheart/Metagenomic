"""
This script was generated to extract artifact into a folder containing HTML"""
######################################################################################################################
###[0] Load required python modules / create arguments to get data and parameters from a user
######################################################################################################################
"""Import Required Modules"""
import argparse, os, subprocess, shutil

"""Create arguments for parsing parameters"""
#Instantiate the parser
parser = argparse.ArgumentParser(description='Description: The script for extracting qiime2 artifact')

#Add Positional Arguments [required parameters]/No Positional arguments

##Add Optional Arguments [optional parameters]
parser.add_argument('--inPath', type=str, default="./", help="Input folder to extract results")

##Get value for each parameters
args = parser.parse_args()

path = args.inPath

"""Summary Input and Output Files"""
print("="*50)
print("SUMMARY INPUT")
print("Input Path:",path)
print("="*50)
######################################################################################################################
###[1] Define Function
######################################################################################################################
"""function to get the index.html link"""
def getLink(initPath):
    fileList = os.listdir(initPath)
    folderName = fileList[0]
    #link = initPath + "/" + folderName + "/data/index.html"
    link = folderName + "/data/index.html"
    return link

######################################################################################################################
###[2] Check Condition and Run Extraction
######################################################################################################################
if os.path.exists(path):
    print("Input path is exists. ==> OK, starting extract QIIME2 results")
    ##################################################################################################################
    """Create HTML output folder"""
    os.mkdir(path+"/outHTML")
    os.mkdir(path+"/outHTML/1_sequence")
    os.mkdir(path+"/outHTML/2_otuTable")
    os.mkdir(path+"/outHTML/3_alphaBetaDiversity")
    os.mkdir(path+"/outHTML/4_taxonomy")
    os.mkdir(path+"/outHTML/5_file")
    ##################################################################################################################
    """[1] Extract raw sequence"""
    cmd = "unzip "+path+"/2_dereplicate_seq.qzv -d "+path+"/outHTML/1_sequence/dereplicate_seq"
    run = subprocess.Popen(cmd,shell="TRUE")
    run.wait()
    link11 = "1_sequence/dereplicate_seq/"+getLink(path+"/outHTML/1_sequence/dereplicate_seq")##dereplicated sequence

    cmd = "unzip "+path+"/2_dereplicate_table.qzv -d "+path+"/outHTML/1_sequence/dereplicate_table"
    run = subprocess.Popen(cmd,shell="TRUE")
    run.wait()
    link12 = "1_sequence/dereplicate_table/"+getLink(path+"/outHTML/1_sequence/dereplicate_table")##dereplicated table
    ##################################################################################################################
    """[2] Extract OTU table"""
    cmd = "unzip "+path+"/4_table_nonchimeric.qzv -d "+path+"/outHTML/2_otuTable/otu_table"
    run = subprocess.Popen(cmd,shell="TRUE")
    run.wait()
    link21 = "2_otuTable/otu_table/"+getLink(path+"/outHTML/2_otuTable/otu_table")##OTU table

    cmd = "unzip "+path+"/4_rep_seq_nonchimeric.qzv -d "+path+"/outHTML/2_otuTable/rep_seq"
    run = subprocess.Popen(cmd,shell="TRUE")
    run.wait()
    link22 = "2_otuTable/rep_seq/"+getLink(path+"/outHTML/2_otuTable/rep_seq")##representative sequence

    cmd = "unzip "+path+"/checkingChimera/stats.qzv -d "+path+"/outHTML/2_otuTable/chimera_stat"
    run = subprocess.Popen(cmd,shell="TRUE")
    run.wait()
    link23 = "2_otuTable/chimera_stat/"+getLink(path+"/outHTML/2_otuTable/chimera_stat")##statistics of chimera checking
    ##################################################################################################################
    """[3] Extract Alpha/Beta Diversity"""
    cmd = "unzip "+path+"/diversityAnalysisResults/alpha-rarefaction.qzv -d "+path+"/outHTML/3_alphaBetaDiversity/alpha_rarefaction"
    run = subprocess.Popen(cmd,shell="TRUE")
    run.wait()
    link31 = "3_alphaBetaDiversity/alpha_rarefaction/"+getLink(path+"/outHTML/3_alphaBetaDiversity/alpha_rarefaction")##alpha rarefaction

    cmd = "unzip "+path+"/diversityAnalysisResults/shannon_vector.qzv -d "+path+"/outHTML/3_alphaBetaDiversity/shannon_vector"
    run = subprocess.Popen(cmd,shell="TRUE")
    run.wait()
    link32 = "3_alphaBetaDiversity/shannon_vector/"+getLink(path+"/outHTML/3_alphaBetaDiversity/shannon_vector")##shannon diversity index

    cmd = "unzip "+path+"/diversityAnalysisResults/observed_otus_vector.qzv -d "+path+"/outHTML/3_alphaBetaDiversity/observed_otus"
    run = subprocess.Popen(cmd,shell="TRUE")
    run.wait()
    link33 = "3_alphaBetaDiversity/observed_otus/"+getLink(path+"/outHTML/3_alphaBetaDiversity/observed_otus")##observed OTUs

    link34 = ""#link for weighted_unifrac_pcoa_results
    link35 = ""#unweighted_unifrac_pcoa_results
    link36 = ""#bray_curtis_pcoa_results
    if os.path.exists(path+"/diversityAnalysisResults/weighted_unifrac_pcoa_results.qzv"):
        cmd = "unzip "+path+"/diversityAnalysisResults/weighted_unifrac_pcoa_results.qzv -d "+path+"/outHTML/3_alphaBetaDiversity/pcoa_weighted_unifrac"
        run = subprocess.Popen(cmd,shell="TRUE")
        run.wait()
        link34 = "3_alphaBetaDiversity/pcoa_weighted_unifrac/"+getLink(path+"/outHTML/3_alphaBetaDiversity/pcoa_weighted_unifrac")##link for weighted_unifrac_pcoa_results

    if os.path.exists(path+"/diversityAnalysisResults/unweighted_unifrac_pcoa_results.qzv"):
        cmd = "unzip "+path+"/diversityAnalysisResults/unweighted_unifrac_pcoa_results.qzv -d "+path+"/outHTML/3_alphaBetaDiversity/pcoa_unweighted_unifrac"
        run = subprocess.Popen(cmd,shell="TRUE")
        run.wait()
        link35 = "3_alphaBetaDiversity/pcoa_unweighted_unifrac/"+getLink(path+"/outHTML/3_alphaBetaDiversity/pcoa_unweighted_unifrac")##link for weighted_unifrac_pcoa_results

    if os.path.exists(path+"/diversityAnalysisResults/bray_curtis_pcoa_results.qzv"):
        cmd = "unzip "+path+"/diversityAnalysisResults/bray_curtis_pcoa_results.qzv -d "+path+"/outHTML/3_alphaBetaDiversity/pcoa_bray_curtis"
        run = subprocess.Popen(cmd,shell="TRUE")
        run.wait()
        link36 = "3_alphaBetaDiversity/pcoa_bray_curtis/"+getLink(path+"/outHTML/3_alphaBetaDiversity/pcoa_bray_curtis")##link for weighted_unifrac_pcoa_results
    ##################################################################################################################
    """[4] Extract Taxonomy"""
    cmd = "unzip "+path+"/7_taxonomy.qzv -d "+path+"/outHTML/4_taxonomy/taxonomy"
    run = subprocess.Popen(cmd,shell="TRUE")
    run.wait()
    link41 = "4_taxonomy/taxonomy/"+getLink(path+"/outHTML/4_taxonomy/taxonomy")##taxonomy

    cmd = "unzip "+path+"/7_taxa_bar_plots.qzv -d "+path+"/outHTML/4_taxonomy/taxonomy_bar_plot"
    run = subprocess.Popen(cmd,shell="TRUE")
    run.wait()
    link42 = "4_taxonomy/taxonomy_bar_plot/"+getLink(path+"/outHTML/4_taxonomy/taxonomy_bar_plot")##barchart taxonomy
    ##################################################################################################################
    """Save output path into HTML file"""
    saveFile = open(path+"/outHTML/index.html","w")
    
    """Define header and Footer"""
    header = """<html><title>Qiime2 Output HTML</title><meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link href='https://fonts.googleapis.com/css?family=RobotoDraft' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<body><style>p {text-indent:20px;}</style>
"""
    footer = "</body></html>"

    """Write Data"""
    saveFile.write(header)
    saveFile.write("<p><strong>###QIIME2 OUTPUT List###</strong></p>")
    saveFile.write("<hr/>")

    saveFile.write("<p><strong>[1] Input Sequence Summary</strong></p>")
    saveFile.write('<p><a href="'+link11+'">1.1 Dereplicated Sequence</a></p>')
    saveFile.write('<p><a href="'+link12+'">1.2 Dereplicated Sequence Table</a></p>')

    saveFile.write("<p><strong>[2] OTU table</strong></p>")
    saveFile.write('<p><a href="'+link21+'">2.1 OTU table</a></p>')
    saveFile.write('<p><a href="'+link22+'">2.2 Representative sequence</a></p>')
    saveFile.write('<p><a href="'+link23+'">2.3 Chimera checking Statistics</a></p>')

    saveFile.write("<p><strong>[3] Alpha/Beta Diversity</strong></p>")
    saveFile.write('<p><a href="'+link31+'">3.1 Rarefaction curve</a></p>')
    saveFile.write('<p><a href="'+link32+'">3.2 Shannon diversity index</a></p>')
    saveFile.write('<p><a href="'+link33+'">3.3 Observed OTU</a></p>')
    if link34 != "":
        saveFile.write('<p><a href="'+link34+'">3.4 PCoA (weighted unifrac)</a></p>')
    if link35 != "":
        saveFile.write('<p><a href="'+link35+'">3.5 PCoA (unweighted unifrac)</a></p>')
    if link36 != "":
        saveFile.write('<p><a href="'+link36+'">3.6 PCoA (bray curtis)</a></p>')

    saveFile.write("<p><strong>[4] Taxonomy</strong></p>")
    saveFile.write('<p><a href="'+link41+'">4.1 Taxonomy</a></p>')
    saveFile.write('<p><a href="'+link42+'">4.2 Barchart plot</a></p>')
    saveFile.write("<hr/>")

    saveFile.write(footer)
    saveFile.close()
    ##################################################################################################################
    """Copy file into folder"""
    #Dereplicated sequence
    fileIn = path+"/outHTML/"+link11.replace("index.html","sequences.fasta")
    fileOut = path+"/outHTML/5_file/dereplicated_sequence.fasta"
    shutil.copy(fileIn,fileOut)

    #Representative sequence
    cmd = "unzip -l "+path+"/4_rep_seq_nonchimeric.qza | grep 'dna-sequences.fasta' | rev | cut -d \" \" -f 1 | rev | xargs -n 1 unzip -c -q "+path+"/4_rep_seq_nonchimeric.qza > "+path+"/outHTML/5_file/representative_sequence.fasta"
    run = subprocess.Popen(cmd,shell="TRUE")
    run.wait()

    #feature-table.biom
    cmd = "unzip -l "+path+"/4_table_nonchimeric.qza | grep 'feature-table.biom' | rev | cut -d \" \" -f 1 | rev | xargs -n 1 unzip -c -q "+path+"/4_table_nonchimeric.qza > "+path+"/outHTML/5_file/feature-table.biom"
    run = subprocess.Popen(cmd,shell="TRUE")
    run.wait()
    #convert .biom to .tsv
    cmd = "biom convert -i "+path+"/outHTML/5_file/feature-table.biom -o "+path+"/outHTML/5_file/feature-table.tsv"+" --to-tsv"
    run = subprocess.Popen(cmd,shell="TRUE")
    run.wait()

    #feature-frequency-detail
    cmd = "unzip -l "+path+"/4_table_nonchimeric.qzv | grep 'feature-frequency-detail.csv' | rev | cut -d \" \" -f 1 | rev | xargs -n 1 unzip -c -q "+path+"/4_table_nonchimeric.qzv > "+path+"/outHTML/5_file/feature-frequency-detail.csv"
    run = subprocess.Popen(cmd,shell="TRUE")
    run.wait()   

    #sample-frequency-detail
    cmd = "unzip -l "+path+"/4_table_nonchimeric.qzv | grep 'sample-frequency-detail.csv' | rev | cut -d \" \" -f 1 | rev | xargs -n 1 unzip -c -q "+path+"/4_table_nonchimeric.qzv > "+path+"/outHTML/5_file/sample-frequency-detail.csv"
    run = subprocess.Popen(cmd,shell="TRUE")
    run.wait()

    #Taxonomy mapping for biom table
    cmd = "unzip -l "+path+"/7_taxonomy.qza | grep 'taxonomy.tsv' | rev | cut -d \" \" -f 1 | rev | xargs -n 1 unzip -c -q "+path+"/7_taxonomy.qza > "+path+"/outHTML/5_file/taxonomy_mapping_for_OTU.tsv"
    run = subprocess.Popen(cmd,shell="TRUE")
    run.wait()

    #Taxonomy
    rank = {1:"kingdom",2:"phylum",3:"class",4:"order",5:"family",6:"genus",7:"species"}
    for i in range(1,8):
        cmd = "unzip -l "+path+"/7_taxa_bar_plots.qzv | grep 'level-"+str(i)+".csv' | rev | cut -d \" \" -f 1 | rev | xargs -n 1 unzip -c -q "+path+"/7_taxa_bar_plots.qzv > "+path+"/outHTML/5_file/taxonomy_level_"+str(i)+"_"+rank[i]+".csv"
        run = subprocess.Popen(cmd,shell="TRUE")
        run.wait()

##End of code

