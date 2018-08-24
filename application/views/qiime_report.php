<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
define('FPDF_FONTPATH', 'font/');

$GLOBALS['numsample'] = "" ;

function hex2dec($couleur = "#000000")
{
    $R = substr($couleur, 1, 2);
    $rouge = hexdec($R);
    $V = substr($couleur, 3, 2);
    $vert = hexdec($V);
    $B = substr($couleur, 5, 2);
    $bleu = hexdec($B);
    $tbl_couleur = array();
    $tbl_couleur['R'] = $rouge;
    $tbl_couleur['V'] = $vert;
    $tbl_couleur['B'] = $bleu;
    return $tbl_couleur;
}

//conversion pixel -> millimeter at 72 dpi
function px2mm($px)
{
    return $px * 25.4 / 72;
}

function txtentities($html)
{
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

    function __construct($orientation = 'P', $unit = 'mm', $format = 'A4')
    {
        //Call parent constructor
        parent::__construct($orientation, $unit, $format);
        //Initialization
        $this->B = 0;
        $this->I = 0;
        $this->U = 0;
        $this->HREF = '';
        $this->fontlist = array('arial', 'times', 'courier', 'helvetica', 'symbol');
        $this->issetfont = false;
        $this->issetcolor = false;
    }// Page header


    var $col = 0;

    function SetCol($col)
    {
        //Set position on top of a column
        $this->col = $col;
        $this->SetLeftMargin(10 + $col * 40);
        $this->SetY(25);
    }

    function AcceptPageBreak()
    {
        //Go to the next column
        $this->SetCol($this->col + 1);
        return false;
    }

    function DumpFont($FontName, $Number)
    {


        $this->SetFont($FontName);
        // $this->Cell(0,5.5,chr($Number),0,1);
        return chr($Number);

    }

    function Header()
    {
//        $this->mypdf->DumpFont('Arial');
//        $this->mypdf->DumpFont('Symbol');
//        $this->mypdf->DumpFont('ZapfDingbats');
        // Logo
        //$this->Image(base_url().'images/logo.jpg',10,6,30);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
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
        $this->SetFont('Arial', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function LoadData()
    {
        // Read file lines
        $lines = file(APPPATH . 'libraries/fpdf/tutorial/countries.txt');
        $dataa = array();
        foreach ($lines as $line)
            $dataa[] = explode(';', trim($line));
        //print_r($dataa);
        return $dataa;
    }


  
    function Table($data)
    {   
         $this->SetFont('','',10);
         for($i=0;$i < count($data); $i++){
              for($j = 0 ; $j < count($data[$i]); $j++){
                 $this->Cell(32, 5, iconv('UTF-8', 'cp874',$data[$i][$j]),1);
              }
            $this->Ln();  
         }

         $GLOBALS['numsample'] = count($data)-1;
        
    }

    function Table2($data)
    {   
         $this->SetFont('','',8);
         for($i=0;$i < count($data); $i++){
              for($j = 0 ; $j < count($data[$i]); $j++){
                
                    $this->Cell(24, 7, iconv('UTF-8', 'cp874',$data[$i][$j]),'1');
                
                 
              }
            $this->Ln();  
         }
        
    }

    function Statistical($header, $dataa)
    {


        // Header
        foreach ($header as $col)
             $this->Cell(20, 7, iconv('UTF-8', 'cp874', $col), 1);
             $this->Ln();  
        // Data
        foreach ($dataa as $row) {
            foreach ($row as $col)
                $this->Cell(40, 7, iconv('UTF-8', 'cp874', $col), 1);
            $this->Ln();
        }


    }

    function WriteHTML($html)
    {
        //HTML parser
        $html = strip_tags($html, "<b><u><i><a><img><p><br><strong><em><font><tr><blockquote>"); //supprime tous les tags sauf ceux reconnus
        $html = str_replace("\n", ' ', $html); //remplace retour à la ligne par un espace
        $a = preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE); //éclate la chaîne avec les balises
        foreach ($a as $i => $e) {
            if ($i % 2 == 0) {
                //Text
                if ($this->HREF)
                    $this->PutLink($this->HREF, $e);
                else
                    $this->Write(6, stripslashes(txtentities($e)));
            } else {
                //Tag
                if ($e[0] == '/')
                    $this->CloseTag(strtoupper(substr($e, 1)));
                else {
                    //Extract attributes
                    $a2 = explode(' ', $e);
                    $tag = strtoupper(array_shift($a2));
                    $attr = array();
                    foreach ($a2 as $v) {
                        if (preg_match('/([^=]*)=["\']?([^"\']*)/', $v, $a3))
                            $attr[strtoupper($a3[1])] = $a3[2];
                    }
                    $this->OpenTag($tag, $attr);
                }
            }
        }
    }

    function OpenTag($tag, $attr)
    {
        //Opening tag
        switch ($tag) {
            case 'STRONG':
                $this->SetStyle('B', true);
                break;
            case 'EM':
                $this->SetStyle('I', true);
                break;
            case 'B':
            case 'I':
            case 'U':
                $this->SetStyle($tag, true);
                break;
            case 'A':
                $this->HREF = $attr['HREF'];
                break;
            case 'IMG':
                if (isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
                    if (!isset($attr['WIDTH']))
                        $attr['WIDTH'] = 0;
                    if (!isset($attr['HEIGHT']))
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
                if (isset($attr['COLOR']) && $attr['COLOR'] != '') {
                    $coul = hex2dec($attr['COLOR']);
                    $this->SetTextColor($coul['R'], $coul['V'], $coul['B']);
                    $this->issetcolor = true;
                }
                if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {
                    $this->SetFont(strtolower($attr['FACE']));
                    $this->issetfont = true;
                }
                break;
        }
    }

    function CloseTag($tag)
    {
        //Closing tag
        if ($tag == 'STRONG')
            $tag = 'B';
        if ($tag == 'EM')
            $tag = 'I';
        if ($tag == 'B' || $tag == 'I' || $tag == 'U')
            $this->SetStyle($tag, false);
        if ($tag == 'A')
            $this->HREF = '';
        if ($tag == 'FONT') {
            if ($this->issetcolor == true) {
                $this->SetTextColor(0);
            }
            if ($this->issetfont) {
                $this->SetFont('arial');
                $this->issetfont = false;
            }
        }
    }

    function SetStyle($tag, $enable)
    {
        //Modify style and select corresponding font
        $this->$tag += ($enable ? 1 : -1);
        $style = '';
        foreach (array('B', 'I', 'U') as $s) {
            if ($this->$s > 0)
                $style .= $s;
        }
        $this->SetFont('', $style);
    }

    function PutLink($URL, $txt)
    {
        //Put a hyperlink
        $this->SetTextColor(0, 0, 255);
        $this->SetStyle('U', true);
        $this->Write(5, $txt, $URL);
        $this->SetStyle('U', false);
        $this->SetTextColor(0);
    }
}


// Instanciation of inherited class
$this->myfpdf = new RPDF();
$this->myfpdf->SetMargins(20, 0);
$this->myfpdf->SetDisplayMode(90);


$this->myfpdf->AddFont('angsa', '', 'angsa.php');
$this->myfpdf->AddFont('angsa', 'B', 'angsab.php');
$this->myfpdf->AddFont('angsa', 'I', 'angsai.php');
$this->myfpdf->AddFont('angsa', 'BI', 'angsaz.php');
$this->myfpdf->AliasNbPages();
$this->myfpdf->AddPage();


//Page 1

$this->myfpdf->SetFont('Times', 'B', 12);
$this->myfpdf->Cell(0, 8, 'Project Name : ' . $project_name, 0, 1);
$this->myfpdf->Cell(0, 8, 'Project type : ' . $project_type, 0, 1);
$this->myfpdf->Cell(0, 8, 'Program analysis : ' . "Qiime", 0, 1);
$this->myfpdf->Cell(0, 8, 'Data pre-processing :', 0, 1);
$this->myfpdf->SetFont('Times', '', 10);
$this->myfpdf->MultiCell(0,6,'The data set '.$project_name.' was uploaded on '.$day.' at '.$time.' and contains  '.$sequences1.' sequences with an average length of '.$average1.' bps.  Raw sequencing data obtained from illumina platform were implemented according to QIIME ver. 1.9.1 (http://qiime.org/). First, raw sequences were joined the reads into contigs using PEAR (Paired-End read mergeR) program. After that the chimeras were also screened using VSEARCH function and removed from the sequences set. The clean sequences were clustered into Operational Taxonomic Units (OTUs). OTUs were picked based on open-reference OTU picking process and representative sequences were generated. Sequences were aligned with PyNAST using Greengenes database and taxonomy assigned to representative sequence using QIIME’s uclust consensus taxonomy assigner and the Greengenes database at a 97% similarity in minimum. The OTUs was subsampled based on the lowest count sequences for alpha and beta analysis. Alpha diversity metrics, including rarefaction curves and taxonomy summaries were calculated using alpha_rarefaction.py script in QIIME. Beta diversity analysis, including Morisitahorn and Jaccard index were calculated using beta_diversity.py script in QIIME.');


$this->myfpdf->SetFont('Times', 'UB', 12);
$this->myfpdf->Cell(0, 10, 'Summary Report', 0, 1);
$this->myfpdf->SetFont('Times', '', 10);
$this->myfpdf->MultiCell(0, 6, 'A total of '.$project_name.' datasets has been submitted to the Amplicon Metagenomic platform. These data were then analysed and display as different diagrams/graphs. This report consist of statistical table and graph of alpha diversity analysis, rarefaction graph, taxonomy profiles of bacterial phyla, heatmap profiling, beta diversity analysis including statistical comparison and PCoA, respectively');

$this->myfpdf->SetFont('Times', 'B', 12);
$this->myfpdf->SetTextColor(220,50,50);
$this->myfpdf->Cell(0, 10, 'Taxonomy classification and alpha diversity analysis', 0, 1);

$this->myfpdf->SetFont('Times', 'B', 10);
$this->myfpdf->SetTextColor(0,0,0);
$this->myfpdf->Cell(0, 6, 'Diversity, richness and composition of microbial communities', 0, 1);

$this->myfpdf->SetFont('Times', '', 10);
$this->myfpdf->MultiCell(0, 6, 'The cleaned sequences were clustered based on OTUs method. After data pre-processing, an average reads length is '.$average1.' bp with number of dataset of '.$seq1.' sequences. The OTUs of these data represented '.$otu_min.'–'.$otu_max.' OTUs per group in average. The alpha diversity was estimated microbial community richness (Chao1) and diversity (Shannon) from subsampling data based on the library size at '.$library_size.'. The '.$chao_max.' and the '.$chao_min.' displayed the highest and the lowest species richness, respectively. The Shannon index estimated the diversity in the community. It displayed that there is the most diverse of bacteria in '.$shannon_max.'  and the lowest diverse of bacteria in '.$shannon_min.'. ');







// Page 2
$this->myfpdf->AddPage();

$this->myfpdf->SetFont('Times', '', 10);

$this->myfpdf->Ln(1);
$this->myfpdf->SetFont('Times', '', 10);
$this->myfpdf->MultiCell(0, 10, $this->myfpdf->WriteHTML('<b>Table 1 </b>Alpha diversity estimator of bacterial 16S analysis at 97% identity threshold') . '');


$this->myfpdf->Table($alpha_diversity);

$this->myfpdf->Image(base_url() .'data_report_qiime/groupA_boxplots_chao.png', '25', '80', '70  ', '70', 'PNG');

$this->myfpdf->Image(base_url() .'data_report_qiime/groupA_boxplots_shannon.png', '110', '80', '70  ', '70', 'PNG');

$this->myfpdf->Cell(0, 90, '', 0, 1);
$this->myfpdf->SetFont('Times', '', 10);
$this->myfpdf->Cell(65, 0, 'Chao', 0, 1,'C');
$this->myfpdf->Cell(250,0, 'Shannon', 0, 1,'C');

$this->myfpdf->Cell(0, 10, '', 0, 1);
$this->myfpdf->MultiCell(1, 8, $this->myfpdf->WriteHTML('<b>Figure 1  </b>Box plots of alpha-diversity estimator based on Chao and Shannon comparing these samples') . '');

$this->myfpdf->MultiCell(0, 6, '        The platform also produce rarefaction graph (Figure 2) which help exhibit a relationship between number of different observed OTUs (y-axis) and tagged samples (x-axis) with the slope of the graph being the ratio between the two axes. This graph allows calculation of species richness to be displayed and compare between each dataset, the indicator for a reliable data is the shape of the curve. When the curve rises and plateau (parallel to x-axis) this pointed out that the data can be trusted and that the information obtained reflected the majority of bacteria. According to the sample submitted, the dataset with the highest level of specie variation is dataset  ' .$observed_max. '  while the lowest is dataset '.$observed_min . '');






//Page 3
$this->myfpdf->AddPage();
$this->myfpdf->Image(base_url() .'data_report_qiime/observedSampleID.png', '50', '14', '100  ', '60', 'PNG');
$this->myfpdf->SetFont('Times', 'B', 10);
$this->myfpdf->Cell(0, 60, '', 0, 1);
$this->myfpdf->Cell(0, 10, $this->myfpdf->WriteHTML('<b>Figure 2  </b> Rarefaction curve of 16S sequences') . '', 0, 1);
$this->myfpdf->SetFont('Times', '', 10);
$this->myfpdf->MultiCell(0, 6, '        Phyla relative abundance plot array all the phylum found in each sample, corresponding to the quantity of each phylum identified (percentage). It displays the phylum abundances for the sequences being read. It can be seen that, phylum  ' . $phylum1 . ' is the most abundances in dataset ' . $phylum1_sam . ',while for dataset  ' . $phylum2_sam1 . '  and  '.$phylum2_sam2.' the most common phylum found was ' . $phylum2 . '.');


$this->myfpdf->Image(base_url() . 'data_report_qiime/charts/bar_plot.png', '20', '120', '100  ', '90', 'PNG');
$this->myfpdf->Image(base_url() . 'data_report_qiime/charts/IDMtkSUWj2F0xoQEOzLwrrTJQJR0Uk_legend.png', '120', '140', '100  ', '60', 'PNG');

$this->myfpdf->Cell(0, 100, '', 0, 1);
$this->myfpdf->SetFont('Times', '', 10);
$this->myfpdf->Cell(0, 10, $this->myfpdf->WriteHTML('<b>Figure 3  </b>Taxonomic classification of bacterial phylum ') . '', 0, 1);
$this->myfpdf->SetFont('Times', '', 10);
$this->myfpdf->MultiCell(0, 6, '      Heatmap (Figure 4) display a graphical representation of a data by assigning a range of values that represent relative abundance of each genus, with different colours. This in turns highlight hidden interactions or trends in the dataset. The data summited indicates that the major genus found in dataset ' .$genus_sam1 . ' is ' . $genus_name1 . ' and their relative abundance was ' . $genus_num1 . '. While for dataset ' . $genus_sam2 . ' the main genus was also found to be ' .$genus_name2. ' with the abundance of ' . $genus_num2 . ' (*number of datasets).');




//Page4
$this->myfpdf->AddPage();
$this->myfpdf->Image(base_url() .'data_report_qiime/abundances.png', '50', '14', '90  ', '95', 'PNG');
$this->myfpdf->SetFont('Times', 'B', 10);
$this->myfpdf->Cell(0, 90, '', 0, 1);
$this->myfpdf->Cell(0, 10, $this->myfpdf->WriteHTML('<b>Figure 4  </b> Relative abundances of the bacterial genera ') . '', 0, 1);

$this->myfpdf->SetFont('Times', 'B', 12);
$this->myfpdf->SetTextColor(220,50,50);
$this->myfpdf->Cell(0, 8, 'Beta diversity analysis', 0, 1);
$this->myfpdf->SetTextColor(0,0,0);

$this->myfpdf->SetFont('Times', 'B', 10);
$this->myfpdf->Cell(0, 8, 'Microbial comparision by beta diversity', 0, 1);

$this->myfpdf->SetFont('Times','',10);
$this->myfpdf->MultiCell(0,6,'The community dissimilarities among different samples which can be described in term of membership and structure are calculated using the calculators: Jaccard, and Morisita-Horn.');
$this->myfpdf->Ln(3);
$this->myfpdf->MultiCell(0, 8, $this->myfpdf->WriteHTML('<b>Table 2 </b> Statistical analysis of beta analysis among '.$GLOBALS['numsample'].' samples based on the '.'##Jaccard##'.' calculators') . '');

$this->myfpdf->Table2($jaccard);




//Page5
$this->myfpdf->AddPage();
$this->myfpdf->SetFont('Times','',10);
$this->myfpdf->MultiCell(0, 15, $this->myfpdf->WriteHTML('<b>Table 3 </b> Statistical analysis of beta analysis among '.$GLOBALS['numsample'].' samples based on the '.'##MorisitaHorn##'.' calculators') . '');

$this->myfpdf->Table2($moris);




$this->myfpdf->SetFont('Times','',10);
$this->myfpdf->Cell(0, 10, '', 0, 1);
$this->myfpdf->MultiCell(0,6,'The ordination methods for community comparison among samples using Principal Coordinates analysis (PCoA) are one of the most common analyses in microbial ecology which were constructed from dissimilarity matrices. PCoA plot compresses all the information (multiple dimension/factors) into a two-dimensional graph with x-axis and y-axis being PC1 and PC2, respectively. This plot indicates the similarity between bacterial community structure. From the analysis, the data shows that the closest relationship occur between dataset '.$horn1.'.'. '');




$this->myfpdf->Image(base_url() . 'data_report_qiime/PC1_vs_PC2_plot.png', '20', '140', '60  ', '60', 'PNG');
$this->myfpdf->Image(base_url() . 'data_report_qiime/PC1_vs_PC3_plot.png', '80', '140', '60  ', '60', 'PNG');
$this->myfpdf->Image(base_url() . 'data_report_qiime/PC3_vs_PC2_plot.png', '140', '140', '60  ', '60', 'PNG');



$this->myfpdf->Cell(0, 80, '', 0, 1);
$this->myfpdf->Cell(0, 10, $this->myfpdf->WriteHTML('<b>Figure 5  </b> PCoA based on '.$GLOBALS['numsample'].' dissimilarity index that shows the bacterial community structure among '.$GLOBALS['numsample'].' samples at 0.03 dissimilarity level') . '', 0, 1);

$this->myfpdf->SetFont('Times', 'B', 12);
$this->myfpdf->SetTextColor(220,50,50);
$this->myfpdf->Cell(0, 8, 'Other statistical analysis', 0, 1);
$this->myfpdf->SetTextColor(0,0,0);

$this->myfpdf->SetFont('Times', '', 10);
$this->myfpdf->MultiCell(0,5,'      Moreover, adonis tests based on xxx were used for beta-diversity comparisons. The result showed adonis test, R2= '.$adonis_r2.', Pr(>F)= '.$adonis_pr.'. Analysis of similarity(ANOSIM) was used to  identify changes in microbial community structures showed anosim test at R test statistic = '.$anosim_test.', p-value '.$anosim_p.'. For the result of PERMANOVA analysis of weighted UniFrac dissimilarity matrices showed pseudo-F test statistic = '.$permanova_test.', p-value at '.$permanova_p. '');


//Page6
$this->myfpdf->AddPage();
$this->myfpdf->SetFont('Times', 'B', 12);
$this->myfpdf->SetTextColor(220,50,50);
$this->myfpdf->Cell(0, 5, 'Functional metabolic analysis', 0, 1);

$this->myfpdf->SetFont('Times', 'B', 10);
$this->myfpdf->SetTextColor(0,0,0);
$this->myfpdf->Cell(0, 10, 'Predicted metabolic functions based on 16S rRNA data using PICRUSt', 0, 1);
$this->myfpdf->SetFont('Times', '', 10);
$this->myfpdf->MultiCell(0,5,'      PICRUSt is an approach for inferring community metagenomic potential from its 16S profile. The predicted metagenome output of PICRUSt was computed in statistic using STAMP v2.0 based on the difference groups. The normalized OTUs data was processed to metagenome prediction and categorized by function using KEGG level 2. These analysis were performed the significantly different between two groups using two-sided Welch’s t-test, the Welch′s inverted test for confidence interval method with Benjamini–Hochberg FDR (BH) for multiple testing corrections. BH was used to correct the potential false positives due to multiple tests. Feature with q-value < 0.05 were considered significant. At level 2 of KEGG, BH correction found '.$my_result.' features displayed significantly different.'. '');


$this->myfpdf->Image(base_url() .'data_report_qiime/bar_plot_STAMP.png', 20, 80, 170, 90);
$this->myfpdf->SetFont('Times', '', 10);
$this->myfpdf->Cell('0', 110, '', 0, 1);
$this->myfpdf->MultiCell(0, 6, $this->myfpdf->WriteHTML('<b>Figure 6  </b> Extended error bar plot of the predicted metagenome functions in KEGG (level 2)') . '');





$this->myfpdf->Output();


?>






