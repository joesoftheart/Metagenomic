<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
define('FPDF_FONTPATH','font/');
class RPDF extends FPDF
{
// Page header
    function Header()
    {
        // Logo
        //$this->Image(base_url().'images/logo.jpg',10,6,30);
        // Arial bold 15
        $this->SetFont('Arial','B',15);
        // Move to the right
        $this->Cell(80);
        // Title
       // $this->Cell(30,10,'Title',1,0,'C');
        // Line break
        $this->Ln(20);
    }

// Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
    function LoadData()
    {
        // Read file lines
        $lines = file(APPPATH.'libraries/fpdf/tutorial/countries.txt');
        $dataa = array();
        foreach($lines as $line)
            $dataa[] = explode(';',trim($line));
        //print_r($dataa);
        return $dataa;
    }


    function Table($header,$data){
        foreach ($header as $col)
            $this->Cell(20, 7, iconv('UTF-8','cp874',$col), 1);
        $this->Ln();
        foreach ($data as $col)
            $this->Cell(20, 7, iconv('UTF-8','cp874',$col), 1);
        $this->Ln();

    }

    function Statistical($header,$dataa)
    {
        // Header
        foreach($header as $col)
            $this->Cell(30, 7, iconv('UTF-8', 'cp874', $col),1);
        $this->Ln();
        // Data
        foreach($dataa as $row)
        {
            foreach($row as $col)
                $this->Cell(30, 7, iconv('UTF-8', 'cp874', $col),1);
            $this->Ln();
        }


    }
}

// Instanciation of inherited class
$this->myfpdf = new RPDF();
$header = array('Joe','Pond','Keap','Bank','Kuk','Ton');
$data = array('หล่อ','พ่อรวย','แฟนสวย','สาวตรึม','ล้อโต','ลูก 1 ');
$headers = array('Joe','Pond','Keap','Bank');
$dataa = $this->myfpdf->LoadData();
$d = array('0' => 'joe','1' => 'joeee', '2' => 'joeeee');
//$dataa[] = $d;
$this->myfpdf->AddFont('angsa', '', 'angsa.php');
$this->myfpdf->AddFont('angsa', 'B', 'angsab.php');
$this->myfpdf->AddFont('angsa', 'I', 'angsai.php');
$this->myfpdf->AddFont('angsa', 'BI', 'angsaz.php');
$this->myfpdf->AliasNbPages();
$this->myfpdf->AddPage();
$this->myfpdf->SetFont('angsa','',18);
$this->myfpdf->Cell(0,10,'Project Name : xxx',0,1);
$this->myfpdf->Cell(0,10,'Project type : 18S/18S/ITS',0,1);
$this->myfpdf->Cell(0,10,'Program analysis : Mothur/Qiime/UPARSE',0,1);
$this->myfpdf->Cell(0,10,'Running mode : standard/advance',0,1);
$this->myfpdf->Cell(0,10,'Data pre-processing :',0,1);
$this->myfpdf->SetFont('angsa','',16);
$this->myfpdf->MultiCell(0, 5, 'The data set xxx was uploaded on 2017-09-16 at 09:54:16 and contains xxx sequences with an average length of 246 bps.  Raw sequencing data obtained from illumina platform were implemented according to Mothur MiSeq SOP. First, raw sequences were joined the reads into contigs using make.contigs function from Mothur program (version 1.39.5). After that they were pre-processed to remove ambiguous base  8. Then, sequences were aligned using SILVA bacterial database. This makes sequences that did not overlap in the desired region are excluded including the sequences of homopolymer bases  8 with the minimum and maximum length of reads were set to xxx and xxx bp, respectively. Moreover, pre.cluster function was used to merge low frequency sequences with very close higher frequency sequencings using a modified single linage algorithm. This is to reduce the sequencing error. The chimeras were also screened using UCHIME function and removed from the sequences set. The clean sequences were classified the taxonomy using the Naïve Bayesian Classifier method, described by Wang et al. with Greengenes database (August 2013 release of gg_13_8_99). The minimum bootstrap confidence score of 80% was used for taxonomic assignment. In the step of the clustering of sequences was performed using phylotype-based methods at genus level with Mothur program.');
$this->myfpdf->SetFont('angsa', 'UB',18);
$this->myfpdf->Cell(0, 10, 'Summary Report',0,1);
$this->myfpdf->SetFont('angsa', '',16);
$this->myfpdf->MultiCell(0, 5, 'A total of ## datasets has been submitted to the Amplicon Metagenomic platform. These data were then analysed and display as different diagrams/graphs. This report consist of statistical table and graph of alpha diversity analysis, rarefaction graph, taxonomy profiles of bacterial phyla, heatmap profiling, beta diversity analysis including venn diagram, statistical comparision, NMDS or PCoA, biplot, respectively.');
$this->myfpdf->SetFont('angsa', 'B', 18);
$this->myfpdf->Cell(0, 10, 'Diversity, richness and composition of microbial communities',0,1);
$this->myfpdf->SetFont('angsa','',16);
$this->myfpdf->MultiCell(0,5,'The cleaned sequences were clustered based on phylotype/OTUs method. After data pre-processing, an average reads length is xxx bp with number of dataset of xxx sequences. The phylotype/OTUs of these data represented xxx–xxx OTUs per group in average. The alpha diversity was estimated microbial community richness (Chao1) and diversity (Shannon) from subsampling data based on the library size at xxx. The xxx and the xxx displayed the highest and the lowest species richness, respectively. The Shannon index estimated the diversity in the community. It displayed that there is the most diverse of bacteria in xxx and the lowest diverse of bacteria in xxx. ');
$this->myfpdf->AddPage();
$this->myfpdf->SetFont('angsa','',18);
$this->myfpdf->Cell(0, 10, 'Table 1 Alpha diversity estimator of bacterial 18S analysis at genus level', 0, 1);
$this->myfpdf->SetFont('angsa','',18);
$this->myfpdf->Table($header,$data);
$this->myfpdf->Image(base_url() . 'images/box_plot.png','10','60','100  ','80','PNG');
$this->myfpdf->Cell(0, 90, '', 0, 1);

$this->myfpdf->SetFont('angsa','',18);
$this->myfpdf->MultiCell(0, 10, ' Figure 1 Box plots of alpha-diversity estimator based on Chao and Shannon comparing these samples');
$this->myfpdf->SetFont('angsa', '', 16);
$this->myfpdf->MultiCell(0, 5, '        The platform also produce rarefaction graph (appendix ##) which help exhibit a relationship between number of different observed OTUs (y-axis) and tagged samples (x-axis) with the slope of the graph being the ratio between the two axes. This graph allows calculation of species richness to be displayed and compare between each dataset, the indicator for a reliable data is the shape of the curve. When the curve rises and plateau (parallel to x-axis) this pointed out that the data can be trusted and that the information obtained reflected the majority of bacteria. According to the sample submitted, the dataset with the highest level of specie variation is dataset ## while the lowest is dataset ##');
$this->myfpdf->AddPage();
$this->myfpdf->Image(base_url() . 'images/rare_plot.png','50','','130  ','110','PNG');
$this->myfpdf->SetFont('angsa', 'B', 18);
$this->myfpdf->Cell(0,80,'',0,1);
$this->myfpdf->Cell(0, 10, 'Figure 2. Rarefaction curve of 18S sequences among the xxx groups', 0, 1);
$this->myfpdf->SetFont('angsa', '', 16);
$this->myfpdf->MultiCell(0,5,'      Phyla relative abundance plot array all the phylum found in each sample, corresponding to the quantity of each phylum identified (percentage). It displays the phylum abundances for the sequences being read. It can be seen that, phylum Phylum name is the most abundances in dataset ##,while for dataset ## and ## the most common phylum found was Phylum name.');
$this->myfpdf->Image(base_url() . 'images/phylumn_plot.png','50','140','110  ','100','');
$this->myfpdf->Cell(0, 100, '',0,1);
$this->myfpdf->SetFont('angsa', '', 18);
$this->myfpdf->Cell(0, 10, 'Figure 3. Taxonomic classification of bacterial phylum in xx groups.', 0, 1);
$this->myfpdf->SetFont('angsa', '', 16);
$this->myfpdf->MultiCell(0,5,'      Heatmap (appendix ##) display a graphical representation of a data by assigning a range of values that represent relative abundance of each genus, with diffe­rent colours. This in turns highlight hidden interactions or trends in the dataset. The data summited indicates that the major genus found in dataset ## is genus name and their relative abundance was #.##. While for dataset ## the main genus was also found to be genus name with the abundance of #.##..... (*number of datasets).');
$this->myfpdf->AddPage();
$this->myfpdf->Image(base_url() . 'images/abun_plot.png','10','','170  ','80','PNG');
$this->myfpdf->SetFont('angsa', 'B', 18);
$this->myfpdf->Cell(0,60,'',0,1);
$this->myfpdf->SetFont('angsa', '', 18);
$this->myfpdf->MultiCell(0, 5, 'Figure 4 Relative abundances of the bacterial genera among xxx groups. The bacterial genus with less than 0.05% as their relative abundance was not shown. The categories of ages were showed in color on the left; navy (teenage), magenta (middle-age), and blue (elderly-age).', 0, 1);
$this->myfpdf->SetFont('angsa','B',18);
$this->myfpdf->Cell(0, 10, 'Microbial comparision by beta diversity', 0, 1);
$this->myfpdf->SetFont('angsa', '', 16);
$this->myfpdf->MultiCell(0,5,'      Venn diagram (appendix ##) show number of unique OTUs identified for each set of data submitted, while the overlapping region represent the shared OTUs between one another. The analysis indicates that dataset ##....and ## have a total of ##...... and ## OTUs, respectively. Some species maybe common and observed in all samples submitted, hence the number will be shown in the most overlapped region; in this case, it’ll be a total of ## OTUs. ');
$this->myfpdf->Image(base_url() . 'images/ven_plot.png','50','140','110  ','80','');
$this->myfpdf->Cell(0, 90, '',0,1);
$this->myfpdf->SetFont('angsa', '', 18);
$this->myfpdf->MultiCell(0, 5, 'Figure 5 Venn diagram that illustrates overlap of OTUs for cheeks, compared between teenage.hea.cheeks, teenage.acn.cheeks1, elderly.hea.cheeks and teenage.acn.cheeks', 0, 1);
$this->myfpdf->Cell(0, 5, '',0,1);
$this->myfpdf->SetFont('angsa', '', 16);
$this->myfpdf->MultiCell(0,5,'      The community dissimilarities among different samples which can be described in term of membership and structure are calculated using the calculators: Lennon, Jclass, Morisita-Horn, Sorenson (sorabund), Smith theta (Thetan), ThetaYC and Bray-Curtis index.');
$this->myfpdf->AddPage();
$this->myfpdf->SetFont('angsa', '', 18);
$this->myfpdf->Cell(0, 10, 'Table 2 Statistical analysis of beta analysis among xxx samples based on the calculators', 0, 1);
$this->myfpdf->Statistical($headers,$dataa);
$this->myfpdf->SetFont('angsa','',16);
$this->myfpdf->MultiCell(0,5,'      Two ordination methods for community comparison among samples such as Principal Coordinates analysis (PCoA) and Non-metric multidimensional scaling (NMDS) are one of the most common analyses in microbial ecology which were constructed from dissimilarity matrices.');
$this->myfpdf->Cell(0, 5, '', 0, 1);
$this->myfpdf->MultiCell(0, 5, 'PCoA and NMDS plot compresses all the information (multiple dimension/factors) into a two-dimensional graph with x-axis and y-axis being MDS1 and MDS2 , PC1 and PC2, respectively. This plot indicates the similarity between bacterial community structure. From the analysis, the data shows that the closest relationship occur between dataset ## and ##. ');
$this->myfpdf->Image(base_url() . 'images/nmds_plot.png',50,145,130,110);
$this->myfpdf->SetFont('angsa','',18);
$this->myfpdf->Cell(0, 120,'',0,1);
$this->myfpdf->MultiCell(0,5,'      Figure 6 NMDS/PCoA based on xxx dissimilarity index that shows the bacterial community structure among xxx samples at 0.03 dissimilarity level/genus level');
$this->myfpdf->AddPage();
$this->myfpdf->MultiCell(0, 5, "        Biplot (appendix ##) present the evaluated data in a graph form. Biplot was visualized from performing using mothur subroutine 'corr.axes'. This visualization help illustrates the interaction between the submitted data and other metadata such as pH, temperature, salinity and the correlation of the relative abundance of each OTU along the two axes in the PCoA or NMDS. The arrow represent the direction of metadata or the environment or OTUs which related among groups to axes of PCoA or NMDS. Different bacteria can interact and respond to changes in metadata in different ways, some may respond more when pH changes but the opposite trend may be observed for salinity due to its high salt tolerance level. This graph will serve as a tool to spot metadata in which it effects one sample more than another, this plot is represent NMDS/PCoA, based on xxx index with biplot, calculated by Spearman/Pearson's correlation.");
$this->myfpdf->Image(base_url().'images/bi_plot.png',50,80,110,90);
$this->myfpdf->Cell(0,85,'',0,1);
$this->myfpdf->MultiCell(0,5,'      Figure 7 NMDS/PCoA based on xxx index with biplot, calculated Spearman/Pearson’s correlation method, representing the direction of metadata or the environment which related with other samples.');
$this->myfpdf->Image(base_url().'images/nmds2_plot.png',50,175,110,90);
$this->myfpdf->Cell(0,95,'',0,1 );
$this->myfpdf->MultiCell(0,5,'      Figure 8. NMDS/PCoA based on xxx index with biplot, calculated Spearman/Pearson’s correlation method, representing the direction of OTUs or genus which related among groups.');
$this->myfpdf->AddPage();
$this->myfpdf->MultiCell(0,5,'      Moreover, the distance-based analysis of molecular variance (AMOVA) or Homogeneity of molecular variance (HOMOVA) are used to assess significant differences among treatment samples. AMOVA testing displayed different bacterial communities between xxx vs xxx, p = 0.082; xxx vs xxx, p = 0.328. Homova testing displayed the difference in variation between the two groups (xxx vs xxx, p=0.023).');
$this->myfpdf->SetFont('angsa', 'B', 18);
$this->myfpdf->Cell(0,10,'Predicted metabolic functions based on 16S rRNA data using PICRUSt',0,1);
$this->myfpdf->SetFont('angsa','',16);
$this->myfpdf->MultiCell(0,5,'PICRUSt is an approach for inferring community metagenomic potential from its 16S profile. The predicted metagenome output of PICRUSt was computed in statistic using STAMP v2.0 based on the difference groups. The normalized OTUs data was processed to metagenome prediction and categorized by function using KEGG level 2. These analysis were performed the significantly different between two groups using two-sided Welch’s t-test, the Welch′s inverted test for confidence interval method with Benjamini–Hochberg FDR (BH) for multiple testing corrections. BH was used to correct the potential false positives due to multiple tests. Feature with q-value < 0.05 were considered significant. At level 2 of KEGG, BH correction found 12 features displayed significantly different between soilsource1 and soilsource2');
$this->myfpdf->Image(base_url() . 'images/stat_plot.png',20,120,170,90);
$this->myfpdf->SetFont('angsa', '', 18);
$this->myfpdf->Cell('0', 120, '', 0, 1);
$this->myfpdf->MultiCell(0, 5, 'Figure 9. Extended error bar plot of the predicted metagenome functions in KEGG (level 2) between soil source 1 and soil source 2');






$this->myfpdf->Output();



?>






