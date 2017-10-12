<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
define('FPDF_FONTPATH','font/');

function hex2dec($couleur = "#000000"){
    $R = substr($couleur, 1, 2);
    $rouge = hexdec($R);
    $V = substr($couleur, 3, 2);
    $vert = hexdec($V);
    $B = substr($couleur, 5, 2);
    $bleu = hexdec($B);
    $tbl_couleur = array();
    $tbl_couleur['R']=$rouge;
    $tbl_couleur['V']=$vert;
    $tbl_couleur['B']=$bleu;
    return $tbl_couleur;
}

//conversion pixel -> millimeter at 72 dpi
function px2mm($px){
    return $px*25.4/72;
}

function txtentities($html){
    $trans = get_html_translation_table(HTML_ENTITIES);
    $trans = array_flip($trans);
    return strtr($html, $trans);
}


class RPDF extends FPDF
{
//variables of html parser
    protected $B;
    protected $I;
    protected $U;
    protected $HREF;
    protected $fontList;
    protected $issetfont;
    protected $issetcolor;

    function __construct($orientation='P', $unit='mm', $format='A4')
    {
        //Call parent constructor
        parent::__construct($orientation,$unit,$format);
        //Initialization
        $this->B=0;
        $this->I=0;
        $this->U=0;
        $this->HREF='';
        $this->fontlist=array('arial', 'times', 'courier', 'helvetica', 'symbol');
        $this->issetfont=false;
        $this->issetcolor=false;
    }// Page header
    function Header()
    {

        // Logo
        //$this->Image(base_url().'images/logo.jpg',10,6,30);
        // Arial bold 15
        $this->SetFont('Arial','B',15);
        // Move to the right
        $this->Cell(80);

        // Title
        //$this->Cell(30,10,'Title',1,0,'C');
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
        foreach($data as $row)
        {
            foreach($row as $col)
                $this->Cell(20, 7, iconv('UTF-8', 'cp874', $col),1);
            $this->Ln();
        }

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
    function WriteHTML($html)
    {
        //HTML parser
        $html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote>"); //supprime tous les tags sauf ceux reconnus
        $html=str_replace("\n",' ',$html); //remplace retour à la ligne par un espace
        $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //éclate la chaîne avec les balises
        foreach($a as $i=>$e)
        {
            if($i%2==0)
            {
                //Text
                if($this->HREF)
                    $this->PutLink($this->HREF,$e);
                else
                    $this->Write(6,stripslashes(txtentities($e)));
            }
            else
            {
                //Tag
                if($e[0]=='/')
                    $this->CloseTag(strtoupper(substr($e,1)));
                else
                {
                    //Extract attributes
                    $a2=explode(' ',$e);
                    $tag=strtoupper(array_shift($a2));
                    $attr=array();
                    foreach($a2 as $v)
                    {
                        if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                            $attr[strtoupper($a3[1])]=$a3[2];
                    }
                    $this->OpenTag($tag,$attr);
                }
            }
        }
    }

    function OpenTag($tag, $attr)
    {
        //Opening tag
        switch($tag){
            case 'STRONG':
                $this->SetStyle('B',true);
                break;
            case 'EM':
                $this->SetStyle('I',true);
                break;
            case 'B':
            case 'I':
            case 'U':
                $this->SetStyle($tag,true);
                break;
            case 'A':
                $this->HREF=$attr['HREF'];
                break;
            case 'IMG':
                if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
                    if(!isset($attr['WIDTH']))
                        $attr['WIDTH'] = 0;
                    if(!isset($attr['HEIGHT']))
                        $attr['HEIGHT'] = 0;
                    $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
                }
                break;
            case 'TR':
            case 'BLOCKQUOTE':
            case 'BR':
                $this->Ln(5);
                break;
            case 'P':
                $this->Ln(10);
                break;
            case 'FONT':
                if (isset($attr['COLOR']) && $attr['COLOR']!='') {
                    $coul=hex2dec($attr['COLOR']);
                    $this->SetTextColor($coul['R'],$coul['V'],$coul['B']);
                    $this->issetcolor=true;
                }
                if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {
                    $this->SetFont(strtolower($attr['FACE']));
                    $this->issetfont=true;
                }
                break;
        }
    }

    function CloseTag($tag)
    {
        //Closing tag
        if($tag=='STRONG')
            $tag='B';
        if($tag=='EM')
            $tag='I';
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF='';
        if($tag=='FONT'){
            if ($this->issetcolor==true) {
                $this->SetTextColor(0);
            }
            if ($this->issetfont) {
                $this->SetFont('arial');
                $this->issetfont=false;
            }
        }
    }

    function SetStyle($tag, $enable)
    {
        //Modify style and select corresponding font
        $this->$tag+=($enable ? 1 : -1);
        $style='';
        foreach(array('B','I','U') as $s)
        {
            if($this->$s>0)
                $style.=$s;
        }
        $this->SetFont('',$style);
    }

    function PutLink($URL, $txt)
    {
        //Put a hyperlink
        $this->SetTextColor(0,0,255);
        $this->SetStyle('U',true);
        $this->Write(5,$txt,$URL);
        $this->SetStyle('U',false);
        $this->SetTextColor(0);
    }
}

// Instanciation of inherited class
$this->myfpdf = new RPDF();
$this->myfpdf->SetMargins(25, 0);
$this->myfpdf->SetDisplayMode(90 );
$header = array('Joe','Pond','Keap','Bank','Kuk','Ton');
$dat = array('หล่อ','พ่อรวย','แฟนสวย','สาวตรึม','ล้อโต','ลูก ');
$data = array();
for ($i = 0;$i<5;$i++){
    $data[] = $dat;
}
//print_r($data);
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



//Add-on



//Page 1

$this->myfpdf->SetFont('Times','B',12);
$this->myfpdf->Cell(0,8,'Project Name : '.$txt,0,1);
$this->myfpdf->Cell(0,8,'Project type : 18S/18S/ITS',0,1);
$this->myfpdf->Cell(0,8,'Program analysis : Mothur/Qiime/UPARSE',0,1);
$this->myfpdf->Cell(0,8,'Running mode : standard/advance',0,1);
$this->myfpdf->Cell(0,8,'Data pre-processing :',0,1);
$this->myfpdf->SetFont('Times','',12);
$this->myfpdf->MultiCell(0, 6, 'The data set xxx was uploaded on 2017-09-16 at 09:54:16 and contains xxx sequences with an average length of 246 bps.  Raw sequencing data obtained from illumina platform were implemented according to Mothur MiSeq SOP. First, raw sequences were joined the reads into contigs using make.contigs function from Mothur program (version 1.39.5). After that they were pre-processed to remove ambiguous base  8. Then, sequences were aligned using SILVA bacterial database. This makes sequences that did not overlap in the desired region are excluded including the sequences of homopolymer bases  8 with the minimum and maximum length of reads were set to xxx and xxx bp, respectively. Moreover, pre.cluster function was used to merge low frequency sequences with very close higher frequency sequencings using a modified single linage algorithm. This is to reduce the sequencing error. The chimeras were also screened using UCHIME function and removed from the sequences set. The clean sequences were classified the taxonomy using the Naïve Bayesian Classifier method, described by Wang et al. with Greengenes database (August 2013 release of gg_13_8_99). The minimum bootstrap confidence score of 80% was used for taxonomic assignment. In the step of the clustering of sequences was performed using phylotype-based methods at genus level with Mothur program.');
$this->myfpdf->SetFont('Times', 'UB',12);
$this->myfpdf->Cell(0, 10, 'Summary Report',0,1);
$this->myfpdf->SetFont('Times', '',12);
$this->myfpdf->MultiCell(0, 6, 'A total of ## datasets has been submitted to the Amplicon Metagenomic platform. These data were then analysed and display as different diagrams/graphs. This report consist of statistical table and graph of alpha diversity analysis, rarefaction graph, taxonomy profiles of bacterial phyla, heatmap profiling, beta diversity analysis including venn diagram, statistical comparision, NMDS or PCoA, biplot, respectively.');
$this->myfpdf->SetFont('Times', 'B', 12);
$this->myfpdf->Cell(0, 10, 'Diversity, richness and composition of microbial communities',0,1);
$this->myfpdf->SetFont('Times','',12);
$this->myfpdf->MultiCell(0,6,'The cleaned sequences were clustered based on phylotype/OTUs method. After data pre-processing, an average reads length is xxx bp with number of dataset of xxx sequences. The phylotype/OTUs of these data represented xxx–xxx OTUs per group in average. The alpha diversity was estimated microbial community richness (Chao1) and diversity (Shannon) from subsampling data based on the library size at xxx. The xxx and the xxx displayed the highest and the lowest species richness, respectively. The Shannon index estimated the diversity in the community. It displayed that there is the most diverse of bacteria in xxx and the lowest diverse of bacteria in xxx. ');



// Page 2
$this->myfpdf->AddPage();
$this->myfpdf->SetFont('Times','',12);
$this->myfpdf->MultiCell(0, 10,$this->myfpdf->WriteHTML('<b>Table 1 </b>Alpha diversity estimator of bacterial 18S analysis at genus level') . '', 0);
$this->myfpdf->SetFont('angsa','',12);
$this->myfpdf->Table($header,$data);
$this->myfpdf->Image(base_url() . 'images/box_plot.png','40','90','110  ','80','PNG');
$this->myfpdf->Cell(0, 120, '', 0, 1);
$this->myfpdf->SetFont('Times','',12);
$this->myfpdf->MultiCell(0, 10, $this->myfpdf->WriteHTML('<b>Figure 1  </b>Box plots of alpha-diversity estimator based on Chao and Shannon comparing these samples') .'');
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->MultiCell(0, 6, '        The platform also produce rarefaction graph (appendix ##) which help exhibit a relationship between number of different observed OTUs (y-axis) and tagged samples (x-axis) with the slope of the graph being the ratio between the two axes. This graph allows calculation of species richness to be displayed and compare between each dataset, the indicator for a reliable data is the shape of the curve. When the curve rises and plateau (parallel to x-axis) this pointed out that the data can be trusted and that the information obtained reflected the majority of bacteria. According to the sample submitted, the dataset with the highest level of specie variation is dataset ## while the lowest is dataset ##');

//Page 3
$this->myfpdf->AddPage();
$this->myfpdf->Image(base_url() . 'images/rare_plot.png','50','14','110  ','80','PNG');
$this->myfpdf->SetFont('Times', 'B', 12);
$this->myfpdf->Cell(0,70,'',0,1);
$this->myfpdf->Cell(0, 10, $this->myfpdf->WriteHTML('<b>Figure 2.</b>Rarefaction curve of 18S sequences among the xxx groups').'', 0, 1);
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->MultiCell(0,6,'      Phyla relative abundance plot array all the phylum found in each sample, corresponding to the quantity of each phylum identified (percentage). It displays the phylum abundances for the sequences being read. It can be seen that, phylum Phylum name is the most abundances in dataset ##,while for dataset ## and ## the most common phylum found was Phylum name.');
$this->myfpdf->Image(base_url() . 'images/phylumn_plot.png','50','130','110  ','100','');
$this->myfpdf->Cell(0, 100, '',0,1);
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->Cell(0, 10, $this->myfpdf->WriteHTML('<b>Figure 3.</b>Taxonomic classification of bacterial phylum in xx groups.').'', 0, 1);
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->MultiCell(0,6,'      Heatmap (appendix ##) display a graphical representation of a data by assigning a range of values that represent relative abundance of each genus, with diffe­rent colours. This in turns highlight hidden interactions or trends in the dataset. The data summited indicates that the major genus found in dataset ## is genus name and their relative abundance was #.##. While for dataset ## the main genus was also found to be genus name with the abundance of #.##..... (*number of datasets).');

//page 4
$this->myfpdf->AddPage();
$this->myfpdf->Image(base_url() . 'images/abun_plot.png','25','14','170  ','80','PNG');
$this->myfpdf->SetFont('Times', 'B', 12);
$this->myfpdf->Cell(0,75,'',0,1);
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->MultiCell(0, 6, $this->myfpdf->WriteHTML('<b>Figure 4 </b>Relative abundances of the bacterial genera among xxx groups. The bacterial genus with less than 0.05% as their relative abundance was not shown. The categories of ages were showed in color on the left; navy (teenage), magenta (middle-age), and blue (elderly-age).').'', 0, 1);
$this->myfpdf->SetFont('Times','B',12);
$this->myfpdf->Cell(0, 10, 'Microbial comparision by beta diversity', 0, 1);
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->MultiCell(0,6,'      Venn diagram (appendix ##) show number of unique OTUs identified for each set of data submitted, while the overlapping region represent the shared OTUs between one another. The analysis indicates that dataset ##....and ## have a total of ##...... and ## OTUs, respectively. Some species maybe common and observed in all samples submitted, hence the number will be shown in the most overlapped region; in this case, it’ll be a total of ## OTUs. ');
$this->myfpdf->Image(base_url() . 'images/ven_plot.png','50','160','110  ','80','');
$this->myfpdf->Cell(0, 80, '',0,1);
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->MultiCell(0, 10, $this->myfpdf->WriteHTML('<b>Figure 5 </b>Venn diagram that illustrates overlap of OTUs for cheeks, compared between teenage.hea.cheeks, teenage.acn.cheeks1, elderly.hea.cheeks and teenage.acn.cheeks').'', 0, 1);
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->MultiCell(0,6,'      The community dissimilarities among different samples which can be described in term of membership and structure are calculated using the calculators: Lennon, Jclass, Morisita-Horn, Sorenson (sorabund), Smith theta (Thetan), ThetaYC and Bray-Curtis index.');


//Page 5
$this->myfpdf->AddPage();
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->Cell(0, 10, $this->myfpdf->WriteHTML('<b>Table 2</b> Statistical analysis of beta analysis among xxx samples based on the calculators').'', 0, 1);
$this->myfpdf->Statistical($headers,$dataa);
$this->myfpdf->SetFont('Times','',12);
$this->myfpdf->MultiCell(0,6,'      Two ordination methods for community comparison among samples such as Principal Coordinates analysis (PCoA) and Non-metric multidimensional scaling (NMDS) are one of the most common analyses in microbial ecology which were constructed from dissimilarity matrices.');
$this->myfpdf->Cell(0, 6, '', 0, 1);
$this->myfpdf->MultiCell(0, 6, 'PCoA and NMDS plot compresses all the information (multiple dimension/factors) into a two-dimensional graph with x-axis and y-axis being MDS1 and MDS2 , PC1 and PC2, respectively. This plot indicates the similarity between bacterial community structure. From the analysis, the data shows that the closest relationship occur between dataset ## and ##. ');
$this->myfpdf->Image(base_url() . 'images/nmds_plot.png',50,150,120,100);
$this->myfpdf->SetFont('Times','',12);
$this->myfpdf->Cell(0, 105,'',0,1);
$this->myfpdf->MultiCell(0,6,$this->myfpdf->WriteHTML('<b>Figure 6</b> NMDS/PCoA based on xxx dissimilarity index that shows the bacterial community structure among xxx samples at 0.03 dissimilarity level/genus level').'      ');

//Page 6
$this->myfpdf->AddPage();
$this->myfpdf->MultiCell(0, 6, "        Biplot (appendix ##) present the evaluated data in a graph form. Biplot was visualized from performing using mothur subroutine 'corr.axes'. This visualization help illustrates the interaction between the submitted data and other metadata such as pH, temperature, salinity and the correlation of the relative abundance of each OTU along the two axes in the PCoA or NMDS. The arrow represent the direction of metadata or the environment or OTUs which related among groups to axes of PCoA or NMDS. Different bacteria can interact and respond to changes in metadata in different ways, some may respond more when pH changes but the opposite trend may be observed for salinity due to its high salt tolerance level. This graph will serve as a tool to spot metadata in which it effects one sample more than another, this plot is represent NMDS/PCoA, based on xxx index with biplot, calculated by Spearman/Pearson's correlation.");
$this->myfpdf->Image(base_url().'images/bi_plot.png',50,80,110,90);
$this->myfpdf->Cell(0,85,'',0,1);
$this->myfpdf->MultiCell(0,6,$this->myfpdf->WriteHTML('<b>Figure 7</b>       NMDS/PCoA based on xxx index with biplot, calculated Spearman/Pearson’s correlation method, representing the direction of metadata or the environment which related with other samples.').'');

//Page 7
$this->myfpdf->AddPage();
$this->myfpdf->Image(base_url().'images/nmds2_plot.png',50,14,110,90);
$this->myfpdf->Cell(0,90,'',0,1);
$this->myfpdf->MultiCell(0,6,$this->myfpdf->WriteHTML('<b>Figure 8.</b>       NMDS/PCoA based on xxx index with biplot, calculated Spearman/Pearson’s correlation method, representing the direction of OTUs or genus which related among groups.').'');
$this->myfpdf->MultiCell(0,6,'      Moreover, the distance-based analysis of molecular variance (AMOVA) or Homogeneity of molecular variance (HOMOVA) are used to assess significant differences among treatment samples. AMOVA testing displayed different bacterial communities between xxx vs xxx, p = 0.082; xxx vs xxx, p = 0.328. Homova testing displayed the difference in variation between the two groups (xxx vs xxx, p=0.023).');
$this->myfpdf->SetFont('Times', 'B', 12);
$this->myfpdf->Cell(0,10,'Predicted metabolic functions based on 16S rRNA data using PICRUSt',0,1);
$this->myfpdf->SetFont('Times','',12);
$this->myfpdf->MultiCell(0,6,'PICRUSt is an approach for inferring community metagenomic potential from its 16S profile. The predicted metagenome output of PICRUSt was computed in statistic using STAMP v2.0 based on the difference groups. The normalized OTUs data was processed to metagenome prediction and categorized by function using KEGG level 2. These analysis were performed the significantly different between two groups using two-sided Welch’s t-test, the Welch′s inverted test for confidence interval method with Benjamini–Hochberg FDR (BH) for multiple testing corrections. BH was used to correct the potential false positives due to multiple tests. Feature with q-value < 0.05 were considered significant. At level 2 of KEGG, BH correction found 12 features displayed significantly different between soilsource1 and soilsource2');

//Page 8
$this->myfpdf->AddPage();
$this->myfpdf->Image(base_url() . 'images/stat_plot.png',20,18,170,90);
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->Cell('0', 100, '', 0, 1);
$this->myfpdf->MultiCell(0, 6, $this->myfpdf->WriteHTML('<b>Figure 9.</b> Extended error bar plot of the predicted metagenome functions in KEGG (level 2) between soil source 1 and soil source 2').'');






$this->myfpdf->Output();



?>






