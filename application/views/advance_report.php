<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
define('FPDF_FONTPATH', 'font/');


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


    function Table($header, $data)
    {
        foreach ($header as $col)
            $this->Cell(20, 7, iconv('UTF-8', 'cp874', $col), 1);
        $this->Ln();
        foreach ($data as $row) {
            foreach ($row as $col)
                $this->Cell(20, 7, iconv('UTF-8', 'cp874', $col), 1);
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
                $this->Cell(20, 7, iconv('UTF-8', 'cp874', $col), 1);
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
$this->myfpdf->SetMargins(25, 0);
$this->myfpdf->SetDisplayMode(90);


$this->myfpdf->AddFont('angsa', '', 'angsa.php');
$this->myfpdf->AddFont('angsa', 'B', 'angsab.php');
$this->myfpdf->AddFont('angsa', 'I', 'angsai.php');
$this->myfpdf->AddFont('angsa', 'BI', 'angsaz.php');
$this->myfpdf->AliasNbPages();
$this->myfpdf->AddPage();


//Add-on


//Page 1
foreach ($projects_t as $r_project) {
    $project_name = $r_project['project_name'];
    $project_type = $r_project['project_type'];
    $project_program = $r_project['project_program'];
    $project_sequencing = $r_project['project_sequencing'];
    $project_analysis = $r_project['project_analysis'];
    $project_date_time = $r_project['project_date_time'];
    $project_num_sam = $r_project['project_num_sam'];
    $project_group_sam = $r_project['project_group_sam'];
    $project_path = $r_project['project_path'];
    $date = explode(" ", $project_date_time);
    $time = $date[1];
    $date = $date[0];
    $methods = null;
    if ($project_analysis == "phylotype") {
        $methods = "genus";
    } else if ($project_analysis == "OTUs") {
        $methods = "0.03";
    }

}


foreach ($projects_run_t as $r_pro_run) {
    $max_amb = $r_pro_run['max_amb'];
    $max_homo = $r_pro_run['max_homo'];
    $min_read = $r_pro_run['min_read'];
    $max_read = $r_pro_run['max_read'];
    $align_seq = $r_pro_run['align_seq'];
    $diffs = $r_pro_run['diffs'];
    $cutoff = $r_pro_run['cutoff'];
    $db_taxon = $r_pro_run['db_taxon'];
    $rm_taxon = $r_pro_run['rm_taxon'];
    $tax_level = $r_pro_run['tax_level'];
    $mode = $r_pro_run['mode'];
    
    $calculator_tree_st = $r_pro_run['calculator_tree_st'];
    $calculator_tree_me = $r_pro_run['calculator_tree_me'];
    $method = $r_pro_run['method'];
    $corr_meta = $r_pro_run['corr_meta'];
    $corr_otu = $r_pro_run['corr_otu'];

    $t_range_otu = $r_pro_run['t_range_otu'];
    $avg_read_pre = $r_pro_run['avg_read_pre'];
    $lib_size = $r_pro_run['lib_size'];
    $min_otu = $r_pro_run['min_otu'];
    $max_otu = $r_pro_run['max_otu'];
    $max_chao = $r_pro_run['max_chao'];
    $min_chao = $r_pro_run['min_chao'];
    $max_shanon = $r_pro_run['max_shanon'];
    $min_shanon = $r_pro_run['min_shanon'];
    $sample_hi = $r_pro_run['sample_hi'];
    $sample_low = $r_pro_run['sample_low'];
    $sample_big_rare = $r_pro_run['sample_big_rare'];
    $name_phylumn = $r_pro_run['name_phylumn'];
    $common_sample_phylumn = $r_pro_run['common_sample_phylumn'];
    $sample_big_phy = $r_pro_run['sample_big_phy'];
    $abun_genus = $r_pro_run['abun_genus'];
    $name_sample_num_ven = $r_pro_run['name_sample_num_ven'];
    $num_otu = $r_pro_run['num_otu'];
    $near_sam = $r_pro_run['near_sam'];
   
    $table_alpha = $r_pro_run['table_alpha'];
    $table_stat = $r_pro_run['table_stat'];
    $name_vs_sam = $r_pro_run['name_vs_sam'];
    $p_value = $r_pro_run['p_value'];
    $name_vs_sam_homo = $r_pro_run['name_vs_sam_homo'];
    $p_value_homo = $r_pro_run['p_value_homo'];
    $count_seqs = $r_pro_run['count_seqs'];
    $avg_lenght = $r_pro_run['avg_lenght'];
    $avg_reads = $r_pro_run['avg_reads'];
    $num_seqs2 = $r_pro_run['num_seqs2'];

}

    $graph = $method;
    $calculators = $calculator_tree_st;
    $calculators_bio = $corr_meta;


$name_vs_sam = preg_split('/:/', $name_vs_sam);
$p_value = preg_split('/:/', $p_value);
$name_patten = null;
$name_patten_homo = null;

$index = 0;
for ($i = 0; $i < count($name_vs_sam); $i++) {
    $name_place = str_replace('-', ' vs ', $name_vs_sam[$index]);
    $p_value_place = "p = " . $p_value[$index];

    if ($name_patten == null) {
        $name_patten = $name_place . "," . $p_value_place . ";";
    } else {
        $name_patten = $name_patten . $name_place . "," . $p_value_place . ";";
    }

}

$name_place_homo = str_replace('-', ' vs ', $name_vs_sam_homo);
$p_value_place_homo = "p = " . $p_value_homo;

$name_patten_homo = $name_place_homo . "," . $p_value_place_homo;


$header = array('groups', 'Good s coverage', 'Observed OTUs', 'Chao', 'Shannon');
$data = array();
for ($i = 0; $i < count($table_alpha); $i++) {
    $data[] = explode(':', $table_alpha[$i]);
}

$headers = array('comparision', '', 'lennon', 'jclass', 'morisitahorn', 'sarabund', 'theten', 'thetayc', 'braycurtis');

$dataa = array();
for ($i = 0; $i < count($table_stat); $i++) {
    $dataa[] = explode(':', $table_stat[$i]);
}


$near_sam = preg_split('/:/', $near_sam);
$near_sam1 = $near_sam[0];
$near_sam2 = $near_sam[1];


$nam_com_phy = "";
for ($i = 0; $i < count($common_sample_phylumn); $i++) {
    $nam_com_phy = $nam_com_phy . " " . $common_sample_phylumn[$i];
}
$index = 0;
$num = 0;
for ($i = 0; $i < count($abun_genus); $i++) {
    $split = preg_split('/:/', $abun_genus[$i]);
    $replce = str_replace('%', '', $split[4]);

    if ($num < $replce) {
        $num = $replce;
        $index = $i;
        $name_dataset = $split[0];
        $name_genus = $split[5];
        $percent = $split[4];
    } else {
    }
}
$name_dataset_genus = "";
$percent_genus = "";
$name_other_genus = "";
for ($i = 0; $i < count($abun_genus); $i++) {
    $split = preg_split('/:/', $abun_genus[$i]);
    $replce = str_replace('%', '', $split[4]);

    if ($i != $index) {

        if ($name_other_genus == "") {
            $name_other_genus = $split[5];
            $percent_genus = $split[4];
            $name_dataset_genus = $split[0];
        } else {
            $percent_genus = $percent_genus . "," . $split[4];
            $name_other_genus = $name_other_genus . "," . $split[5];
            $name_dataset_genus = $name_dataset_genus . "," . $split[0];
        }
    }

}

$name_dataset_otu = "";
$num_dataset_otu = "";
for ($i = 0; $i < count($name_sample_num_ven); $i++) {
    $split = preg_split('/:/', $abun_genus[$i]);
    if ($name_dataset_otu == "") {
        $name_dataset_otu = $split[0];
    } else {
        $name_dataset_otu = $name_dataset_otu . "," . $split[0];
    }

    if ($num_dataset_otu == "") {
        $num_dataset_otu = $split[1];
    } else {
        $num_dataset_otu = $num_dataset_otu . "," . $split[1];
    }


}


// echo "dbvdfbdb";

$this->myfpdf->SetFont('Times', 'B', 12);
$this->myfpdf->Cell(0, 8, 'Project Name : ' . $project_name, 0, 1);
$this->myfpdf->Cell(0, 8, 'Project type : ' . $project_type, 0, 1);
$this->myfpdf->Cell(0, 8, 'Program analysis : ' . $project_program, 0, 1);
$this->myfpdf->Cell(0, 8, 'Running mode : ' . $mode, 0, 1);
$this->myfpdf->Cell(0, 8, 'Data pre-processing :', 0, 1);
//$this->myfpdf->DumpFont('Symbol', '179');
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->MultiCell(0, 6, 'The data set ' . $project_name . ' was uploaded on ' . $date . ' at ' . $time . ' and contains ' . $count_seqs . ' sequences with an average length of ' . $avg_lenght . ' bps.  Raw sequencing data obtained from ' . $project_sequencing . ' platform were implemented according to ' . $project_program . ' MiSeq SOP. First, raw sequences were joined the reads into contigs using make.contigs function from Mothur program (version 1.39.5). After that they were pre-processed to remove ambiguous base > ' . $max_amb . '. Then, sequences were aligned using SILVA bacterial database. This makes sequences that did not overlap in the desired region are excluded including the sequences of homopolymer bases > ' . $max_amb . ' with the minimum and maximum length of reads were set to ' . $min_read . ' and ' . $max_read . ' bp, respectively. Moreover, pre.cluster function was used to merge low frequency sequences with very close higher frequency sequencings using a modified single linage algorithm. This is to reduce the sequencing error. The chimeras were also screened using UCHIME function and removed from the sequences set. The clean sequences were classified the taxonomy using the Naïve Bayesian Classifier method, described by Wang et al. with Greengenes database (August 2013 release of gg_13_8_99). The minimum bootstrap confidence score of 80% was used for taxonomic assignment. In the step of the clustering of sequences was performed using ' . $project_type . '-based methods at ' . $methods . ' level with Mothur program.');
$this->myfpdf->SetFont('Times', 'UB', 12);
$this->myfpdf->Cell(0, 10, 'Summary Report', 0, 1);
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->MultiCell(0, 6, 'A total of ' . $project_group_sam . ' datasets has been submitted to the Amplicon Metagenomic platform. These data were then analysed and display as different diagrams/graphs. This report consist of statistical table and graph of alpha diversity analysis, rarefaction graph, taxonomy profiles of bacterial phyla, heatmap profiling, beta diversity analysis including venn diagram, statistical comparision, NMDS or PCoA, biplot, respectively.');
$this->myfpdf->SetFont('Times', 'B', 12);
$this->myfpdf->Cell(0, 10, 'Diversity, richness and composition of microbial communities', 0, 1);
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->MultiCell(0, 6, 'The cleaned sequences were clustered based on ' . $project_analysis . ' method. After data pre-processing, an average reads length is ' . $num_seqs2 . ' bp with number of dataset of ' . $avg_reads . ' sequences. The ' . $project_analysis . ' of these data represented ' . $t_range_otu . ' OTUs per group in average. The alpha diversity was estimated microbial community richness (Chao1) and diversity (Shannon) from subsampling data based on the library size at '. $lib_size .'. The ' . $max_chao . ' and the ' . $min_chao . ' displayed the highest and the lowest species richness, respectively. The Shannon index estimated the diversity in the community. It displayed that there is the most diverse of bacteria in ' . $max_shanon . ' and the lowest diverse of bacteria in ' . $min_shanon . '. ');


// Page 2
$this->myfpdf->AddPage();
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->MultiCell(0, 10, $this->myfpdf->WriteHTML('<b>Table 1 </b>Alpha diversity estimator of bacterial 16S analysis at ' . $methods . ' level') . '', 0);
$this->myfpdf->SetFont('angsa', '', 12);
$this->myfpdf->Table($header, $data);
$this->myfpdf->Image(base_url() .'img_user/aumza/testBiom2/Alpha.png', '40', '90', '110  ', '70', 'PNG');
$this->myfpdf->Cell(0, 120, '', 0, 1);
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->MultiCell(0, 10, $this->myfpdf->WriteHTML('<b>Figure 1  </b>Box plots of alpha-diversity estimator based on Chao and Shannon comparing these samples') . '');
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->MultiCell(0, 6, '        The platform also produce rarefaction graph (appendix ##) which help exhibit a relationship between number of different observed OTUs (y-axis) and tagged samples (x-axis) with the slope of the graph being the ratio between the two axes. This graph allows calculation of species richness to be displayed and compare between each dataset, the indicator for a reliable data is the shape of the curve. When the curve rises and plateau (parallel to x-axis) this pointed out that the data can be trusted and that the information obtained reflected the majority of bacteria. According to the sample submitted, the dataset with the highest level of specie variation is dataset ' . $sample_hi . ' while the lowest is dataset ' . $sample_low . '');

//Page 3
$this->myfpdf->AddPage();
$this->myfpdf->Image(base_url() .'img_user/aumza/testBiom2/Rare.png', '50', '14', '90  ', '60', 'PNG');
$this->myfpdf->SetFont('Times', 'B', 12);
$this->myfpdf->Cell(0, 50, '', 0, 1);
$this->myfpdf->Cell(0, 10, $this->myfpdf->WriteHTML('<b>Figure 2.</b>Rarefaction curve of 18S sequences among the ' . $sample_big_rare . ' groups') . '', 0, 1);
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->MultiCell(0, 6, '      Phyla relative abundance plot array all the phylum found in each sample, corresponding to the quantity of each phylum identified (percentage). It displays the phylum abundances for the sequences being read. It can be seen that, phylum ' . $name_phylumn . ' is the most abundances in dataset ' . $nam_com_phy . ',while for dataset ' . $nam_com_phy . ' and ## the most common phylum found was ' . $name_phylumn . '.');
$this->myfpdf->Image(base_url() . 'img_user/aumza/testBiom2/Abun.png', '50', '120', '100  ', '90', '');
$this->myfpdf->Cell(0, 100, '', 0, 1);
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->Cell(0, 10, $this->myfpdf->WriteHTML('<b>Figure 3.</b>Taxonomic classification of bacterial phylum in ' . $sample_big_phy . ' groups.') . '', 0, 1);
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->MultiCell(0, 6, '      Heatmap (appendix ##) display a graphical representation of a data by assigning a range of values that represent relative abundance of each genus, with diffe­rent colours. This in turns highlight hidden interactions or trends in the dataset. The data summited indicates that the major genus found in dataset ' . $name_dataset . ' is ' . $name_genus . ' and their relative abundance was ' . $percent . '. While for dataset ' . $name_dataset_genus . ' the main genus was also found to be ' . $name_other_genus . ' with the abundance of ' . $percent_genus . ' (*number of datasets).', '', 'L');

//page 4
$this->myfpdf->AddPage();
$this->myfpdf->Image(base_url() . 'img_user/aumza/testBiom2/heartmap.png', '50', '5', '100  ', '80', 'PNG');
$this->myfpdf->SetFont('Times', 'B', 12);
$this->myfpdf->Cell(0, 73, '', 0, 1);
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->MultiCell(0, 6, $this->myfpdf->WriteHTML('<b>Figure 4 </b>Relative abundances of the bacterial genera among ' . $project_num_sam . ' groups. The bacterial genus with less than 0.05% as their relative abundance was not shown.') . '', 0, 1);
$this->myfpdf->SetFont('Times', 'B', 12);
$this->myfpdf->Cell(0, 10, 'Microbial comparision by beta diversity', 0, 1);
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->MultiCell(0, 6, '      Venn diagram (appendix ##) show number of unique OTUs identified for each set of data submitted, while the overlapping region represent the shared OTUs between one another. The analysis indicates that dataset ' . $name_dataset_otu . ' have a total of ' . $num_dataset_otu . ' and ## OTUs, respectively. Some species maybe common and observed in all samples submitted, hence the number will be shown in the most overlapped region; in this case, it’ll be a total of ' . $num_otu . ' OTUs. ', '', 'L');
$this->myfpdf->Image(base_url() .'img_user/aumza/testBiom2/sharedsobs.png', '50', '160', '90  ', '70', '');
$this->myfpdf->Cell(0, 80, '', 0, 1);
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->MultiCell(0, 10, $this->myfpdf->WriteHTML('<b>Figure 5 </b>Venn diagram that illustrates overlap of OTUs, compared between ' . $name_dataset_otu) . '', 0, 1);
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->MultiCell(0, 6, '      The community dissimilarities among different samples which can be described in term of membership and structure are calculated using the calculators: Lennon, Jclass, Morisita-Horn, Sorenson (sorabund), Smith theta (Thetan), ThetaYC and Bray-Curtis index.');


//Page 5
$this->myfpdf->AddPage();
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->Cell(0, 10, $this->myfpdf->WriteHTML('<b>Table 2</b> Statistical analysis of beta analysis among ' . $project_num_sam . ' samples based on the calculators') . '', 0, 1);
$this->myfpdf->SetFont('Times', '', 9);
$this->myfpdf->Statistical($headers, $dataa);
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->MultiCell(0, 6, '      Two ordination methods for community comparison among samples such as Principal Coordinates analysis (PCoA) and Non-metric multidimensional scaling (NMDS) are one of the most common analyses in microbial ecology which were constructed from dissimilarity matrices.');
$this->myfpdf->Cell(0, 6, '', 0, 1);
$this->myfpdf->MultiCell(0, 6, 'PCoA and NMDS plot compresses all the information (multiple dimension/factors) into a two-dimensional graph with x-axis and y-axis being MDS1 and MDS2 , PC1 and PC2, respectively. This plot indicates the similarity between bacterial community structure. From the analysis, the data shows that the closest relationship occur between dataset ' . $near_sam1 . ' and ' . $near_sam2 . '. ');
$this->myfpdf->Image(base_url() .'img_user/aumza/testBiom2/NMD_thetayc.png', 50, 150, 120, 90);
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->Cell(0, 105, '', 0, 1);
$this->myfpdf->MultiCell(0, 6, $this->myfpdf->WriteHTML('<b>Figure 6</b> ' . $graph . ' based on ' . $calculators . ' dissimilarity index that shows the bacterial community structure among ' . $project_num_sam . ' samples at ' . $methods . ' level') . '.');

//Page 6
$this->myfpdf->AddPage();
$this->myfpdf->MultiCell(0, 6, "        Biplot (appendix ##) present the evaluated data in a graph form. Biplot was visualized from performing using mothur subroutine 'corr.axes'. This visualization help illustrates the interaction between the submitted data and other metadata such as pH, temperature, salinity and the correlation of the relative abundance of each OTU along the two axes in the PCoA or NMDS. The arrow represent the direction of metadata or the environment or OTUs which related among groups to axes of PCoA or NMDS. Different bacteria can interact and respond to changes in metadata in different ways, some may respond more when pH changes but the opposite trend may be observed for salinity due to its high salt tolerance level. This graph will serve as a tool to spot metadata in which it effects one sample more than another, this plot is represent $graph, based on $calculators index with biplot, calculated by $calculators_bio's correlation.");
$this->myfpdf->Image(base_url() .'img_user/aumza/testBiom2/NMDS_BiplotwithMetadata_thetayc.png', 50, 80, 110, 90);
$this->myfpdf->Cell(0, 85, '', 0, 1);
$this->myfpdf->MultiCell(0, 6, $this->myfpdf->WriteHTML('<b>Figure 7</b>       ' . $graph . ' based on ' . $calculators . ' index with biplot, calculated ' . $calculators_bio . '’s correlation method, representing the direction of metadata or the environment which related with other samples.') . '');

//Page 7
$this->myfpdf->AddPage();
$this->myfpdf->Image(base_url() .'img_user/aumza/testBiom2/NMDS_BiplotwithOTU_thetayc.png', 50, 14, 110, 90);
$this->myfpdf->Cell(0, 90, '', 0, 1);
$this->myfpdf->MultiCell(0, 6, $this->myfpdf->WriteHTML('<b>Figure 8.</b>       ' . $graph . ' based on ' . $calculators . ' index with biplot, calculated ' . $calculators_bio . '’s correlation method, representing the direction of OTUs or genus which related among groups.') . '');
$this->myfpdf->MultiCell(0, 6, '      Moreover, the distance-based analysis of molecular variance (AMOVA) or Homogeneity of molecular variance (HOMOVA) are used to assess significant differences among treatment samples. AMOVA testing displayed different bacterial communities between ' . $name_patten . '. Homova testing displayed the difference in variation between the two groups ' . $name_patten_homo . '.');
$this->myfpdf->SetFont('Times', 'B', 12);
$this->myfpdf->Cell(0, 10, 'Predicted metabolic functions based on 16S rRNA data using PICRUSt', 0, 1);
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->MultiCell(0, 6, 'PICRUSt is an approach for inferring community metagenomic potential from its 16S profile. The predicted metagenome output of PICRUSt was computed in statistic using STAMP v2.0 based on the difference groups. The normalized OTUs data was processed to metagenome prediction and categorized by function using KEGG level 2. These analysis were performed the significantly different between two groups using two-sided Welch’s t-test, the Welch′s inverted test for confidence interval method with Benjamini–Hochberg FDR (BH) for multiple testing corrections. BH was used to correct the potential false positives due to multiple tests. Feature with q-value < 0.05 were considered significant. At level 2 of KEGG, BH correction found 12 features displayed significantly different between soilsource1 and soilsource2');

//Page 8
$this->myfpdf->AddPage();
$this->myfpdf->Image(base_url() .'img_user/aumza/testBiom2/bar_plot_STAMP.png', 20, 18, 170, 90);
$this->myfpdf->SetFont('Times', '', 12);
$this->myfpdf->Cell('0', 100, '', 0, 1);
$this->myfpdf->MultiCell(0, 6, $this->myfpdf->WriteHTML('<b>Figure 9.</b> Extended error bar plot of the predicted metagenome functions in KEGG (level 2) between soil source 1 and soil source 2') . '');


// Page Test


$this->myfpdf->Output();


?>






