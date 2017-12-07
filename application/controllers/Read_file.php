<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/9/17
 * Time: 5:39 PM
 */
class Read_file extends CI_Controller{


    public function __construct()
    {

        parent::__construct();
        $this->load->helper('url');
        //      $this->load->helper('file');

    }

    public function index(){
        $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');

        $this->load->view('header',$data);
        $this->load->view('read_file');
        $this->load->view('footer');

    }

    // This upload file type
    public function upload_file(){
        $config['upload_path'] = 'uploads/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = '1024';
        $config['max_width'] = '1024';
        $config['max_height'] = '1024';
        $this->load->library('upload');
        $this->upload->initialize($config);

        if ($this->upload->do_upload("pictures")) {
            $data = $this->upload->data();

        }else{
            echo $this->upload->display_errors();
        }
    }


    public function read_file_phylotype(){

        $path_file_phylotype = array("final.tx.summary","file_after_reverse.csv","file_phylum_count.txt","final.tx.2.subsample.shared",
            "final.tx.thetayc.2.lt.ave.nmds.axes","final.tx.groups.summary","final.tx.groups.rarefaction","soilpro.pearson.corr.axes");

        $path_file_otu = array("final.opti_mcc.summary","file_after_reverse.csv","file_phylum_count.txt","final.opti_mcc.0.03.subsample.shared",
            "final.opti_mcc.thetayc.0.03.lt.ave.nmds.axes","final.opti_mcc.groups.summary","final.opti_mcc.groups.rarefaction","soilpro.pearson.corr.axes");

        $path_owncloud_phylotype = "owncloud/data/joesoftheart/files/SAMPLE-WES-2023/output/";
        $path_owncloud_otu = "owncloud/data/joesoftheart/files/SAMPLE-OTU/output/";


        $chao_shanon = file_get_contents($path_owncloud_phylotype."final.tx.groups.ave-std.summary");
        $row = explode("\n", $chao_shanon);
        array_shift($row);
        $arr_out = array() ;
        $arr_chao = array();
        $arr_shanon = array();
        $index =0;
        foreach ($row as $value => $data){
            if ($data != null){
                $row_data = preg_split("/\s+/", $data);

                if($row_data[0] == 2) {
                    $arr_out[$index] = $row_data[5];
                    $arr_chao[$index] = $row_data[9];
                    $arr_shanon[$index] = $row_data[12];
                    $index++;
                }
            }

        }


        echo "Min otu :" . min($arr_out)."<br>";
        echo "Max otu :" . max($arr_out)."<br>";
        echo "Min chao :" . min($arr_chao)."<br>";
        echo "Max chao :" . max($arr_chao)."<br>";
        echo "Min shanon :" . min($arr_shanon)."<br>";
        echo "Max shanon :" . max($arr_shanon)."<br>";
        echo ">>>>>>>>>>>>>>>>>>>>>End Page 1-2<<<<<<<<<<<<<<<<<br>";


        $rare_index = array();
        $name_sample = array();
        $save_value = array();
        $rarefaction = file_get_contents($path_owncloud_phylotype."final.tx.groups.rarefaction");
        $row = explode("\n", $rarefaction);
        $check_index = preg_split('/\s+/', $row[0]);
          for($i=0;$i<count($check_index);$i++){
            $save_index =   explode('-', $check_index[$i]);
              if ($save_index[0] == "2") {
                  array_push($rare_index, $i);
                  array_push($name_sample, $save_index[1]);
              }
          }

        array_shift($row);
        foreach ($row as $value => $data){
            if ($data != null){
                $row_data_rare = preg_split('/\s+/', $data);
                for ($i=0;$i<count($rare_index);$i++){
                        if (is_numeric($row_data_rare[$rare_index[$i]])) {
                                if (isset($save_value[$i])){
                                }else{
                                    $save_value[$i] = "0";
                                }
                            if ($save_value[$i] < $row_data_rare[$rare_index[$i]]) {
                                $save_value[$i] = $row_data_rare[$rare_index[$i]];
                            } else {

                            }
                        } else {
                            if ($save_value[$i] == null) {
                                $save_value[$i] = "0";
                            } else {

                            }
                        }
                }
            }
        }
        echo " Name sam :" . $name_sample[0]."  Value :".$save_value[0];
        echo '<br>';
        echo " Name sam :" .$name_sample[1]."  Value :".$save_value[1];
        echo '<br>';
        echo " Name sam :" .$name_sample[2]."  Value :".$save_value[2];
        echo '<br>';
        echo " Name sam :" .$name_sample[3]."  Value :".$save_value[3];
        echo '<br>';
        $name_sample_phylumn = array();
        $value_phylumn =[];
        $save_value_phylumn = array();
        $total_num = null;

        $phylumn_count = file_get_contents($path_owncloud_phylotype."file_phylum_count.txt");
        $row = explode("\n", $phylumn_count);
        $check_index = preg_split('/\s+/', $row[0]);
        array_shift($row);
        $half_sam = count($check_index)/2;
        for($i=0;$i<count($check_index);$i++){
           $name_sample_phylumn[$i] = $check_index[$i];
        }
            $index = 0;
        foreach ($row as $value => $data){
            $row_data_phy = preg_split('/\s+/', $data);

            if ($data != null) {
                $total_num = null;
                for ($i = 0; $i < count($name_sample_phylumn); $i++) {
                             $value_phylumn[$index][$i] = $row_data_phy[$i] . '<br>';
                    $total_num += $row_data_phy[$i];
                }
                $save_value_phylumn[$index] =  $total_num;
                // echo '<br>';
            }
            $index++;
        }
$max = 0;
$k = 0;
$more = 0;
        $name_phylumn = null;
        foreach ($save_value_phylumn as $key => $value) {

            if ($value >= $max) {
                $max = max($save_value_phylumn);
                $k = $key;
            }
        }
       for ($n = 0;$n<count($name_sample_phylumn);$n++){
            if ($n == 0){
                $name_phylumn = $value_phylumn[$k][$n];
            }
           echo $value_phylumn[$k][$n];
           if ($value_phylumn[$k][$n] > $more){
               $more = $value_phylumn[$k][$n];
           }
       }
        for ($n = 1;$n<count($name_sample_phylumn);$n++){
            echo "Name sample :".$name_sample_phylumn[$n]."  Value :".$value_phylumn[$k][$n].'<br>';
        }
        echo "Big SAM :".$more;
        echo '<br>';

        echo ">>>>>>>>>>>>>>>>>>>>>End Page 3<<<<<<<<<<<<<<<<<br>";
        $genus = file_get_contents($path_owncloud_phylotype."file_after_reverse.csv");
        $row = explode("\n", $genus);
        $row2 = explode("\n", $genus);
        $row_name = explode("\n", $genus);
        array_shift($row);
        array_shift($row2);
        $count_genus = array();
        $count_genus2 = array();
        $num = 0;
        $key_sam = null;
        $max_for_sam = array();
        foreach ($row as $value => $data) {
            if ($data != null ) {
                $split = preg_split('/,/', $data);
                if ($count_genus == null) {
                    for ($j = 1; $j < count($split); $j++) {
                        $count_genus[$j] =  $split[$j];
                        $count_genus2[$j] =  $split[$j];
                    }
                }else{
                    for ($j = 1; $j < count($split); $j++) {
                        $count_genus[$j] +=  $split[$j];
                        $count_genus2[$j] =  $split[$j];
                    }
                }
                $num = 0;
                $key_index = null;

                for ($k = 1; $k < count($count_genus2); $k++) {
                    if ($num < $count_genus2[$k]){
                        $num = $count_genus2[$k];
                        $key_index = $k;
                    }
                }

                $total = 0;
                foreach ($row2 as $value_e => $data_a){
                    if ($data_a != null ) {
                        $split1 = preg_split('/,/', $data_a);
                        $total += $split1[$key_index];
                    }
                }

                $splitn = preg_split('/,/', $row_name[0]);
                $genus_name = $splitn[$key_index];

                $maxximun = max($count_genus2);
                $percent = $maxximun * 100 / $total;


                echo "SAM :" . $split[0] ."MAX :" . $maxximun  . "INDEX :" . $key_index . "TOTAL : ". $total ."PERCENT :" . round($percent,2)."%" ."GENUS NAME :" . $genus_name;
                echo '<br>';
            }
        }

        $svg_file = file_get_contents($path_owncloud_phylotype . "final.tx.2.subsample.2.sharedsobs.S1_1_16s_S1-S2_1_16s_S3-S3_1_16s_S5-S4_1_16s_S7.svg");

        $find_string   = '<text';
        $position = strpos($svg_file, $find_string);

        $svg_file_new = substr($svg_file, $position);

        $searchfor = 'The number of species in group';
        $searchfor2 = 'The total richness of all';
        $pattern = preg_quote($searchfor, '/');
// finalise the regular expression, matching the whole line
        $pattern = "/^.*$pattern.*\$/m";
// search, and store all matching occurences in $matches
        if(preg_match_all($pattern, $svg_file, $matches)){

            foreach ($matches[0] as $value => $data_otu){
                  $name_sam_otu = preg_split('/\s+/', $data_otu);
                echo $name_sam_otu[11] . " : " . $name_sam_otu[13].'<br>';
            }

        }
        else{
            echo "No matches found";
        }



        $svg_file2 = file_get_contents($path_owncloud_phylotype . "final.tx.2.subsample.2.sharedsobs.S1_1_16s_S1-S2_1_16s_S3-S3_1_16s_S5-S4_1_16s_S7.sharedotus");
                $row_otu = explode("\n", $svg_file2);
        foreach ($row_otu as $value => $data){
            if ($data != null) {
                $split_row_otu = preg_split('/\s+/', $data);
                $num_outs = $split_row_otu[1];

            }


        }
        echo "Num tou :" . $num_outs.'<br>';

        echo ">>>>>>>>>>>>>>>>>>>>>End Page 4 <<<<<<<<<<<<<<<<<br>";
        $axes1 = array();
        $axes2 = array();
        $near_data = file_get_contents($path_owncloud_phylotype."final.tx.thetayc.2.lt.ave.nmds.axes");
        $row = explode("\n", $near_data);
        array_shift($row);
        $index = 0;
        foreach ($row as $value => $data) {

            if ($data != null) {
                $split = preg_split('/\s+/', $data);
              $axes1[$index] = $split[1];
               $axes2[$index] = $split[2];
             $index++;
            }
        }

        $sam1 = null;
        $sam2 = null;
        $save_near_county = 0 ;
        for ($i = 0; $i < count($axes2); $i++) {

            for ($j = 0; $j < count($axes2); $j++) {

                if ($i != $j) {
                    if ($save_near_county == null) {
                        $save_near_county =  sqrt(pow((($axes1[$i]) - ($axes1[$j])),2) + pow( (($axes2[$i]) - ($axes2[$j])),2));

                    }else{
                        if ( $save_near_county > sqrt(pow((($axes1[$i]) - ($axes1[$j])),2) + pow((($axes2[$i]) - ($axes2[$j])),2 ))){
                                $save_near_county =  sqrt(pow((($axes1[$i]) - ($axes1[$j])),2) + pow((($axes2[$i]) - ($axes2[$j])),2));
//                            echo ($axes1[$i]) - ($axes1[$j]);
//                            echo '<br>';
//                            echo $axes2[$i] - $axes2[$j];
//                            echo '<br>';
//                            $sam1 =  $i ;
//                            $sam2 = $j;
//                            echo '<br>';
//                            echo $save_near_county;
                        }else{

                        }
                    }
                }
            }

        }

        $index = 0;
        foreach ($row as $value => $data) {

            if ($data != null) {
                $split = preg_split('/\s+/', $data);
                if ($index == $sam1){
                    echo "Sam1". $split[0];
                    echo '<br>';
                }
                if ($index == $sam2){
                    echo "Sam2". $split[0];
                    echo '<br>';
                }
                $index++;
            }
        }

        echo ">>>>>>>>>>>>>>>>>>>>>End Page 5 <<<<<<<<<<<<<<<<<br>";
        echo "In db";
        echo ">>>>>>>>>>>>>>>>>>>>>End Page 6 <<<<<<<<<<<<<<<<<br>";
        echo "In db";
        echo ">>>>>>>>>>>>>>>>>>>>>End Page 7 <<<<<<<<<<<<<<<<<br>";

        foreach ($path_file_phylotype as $value){
            if (file_exists($path_owncloud_phylotype . $value)) {
                echo "Have file". $value;
                echo "<br>";
//                $file = fopen($path_owncloud_phylotype . $value,"r") or die('Unable file');
//                echo fread($file, filesize($path_owncloud_phylotype . $value));
//
//
//
//                fclose($file);

               // echo $file;
            }else{
                echo "No file". $value;
                echo "<br>";
            }
        }
        echo "____________________";
        echo "<br>";
        foreach ($path_file_otu as $value){
            if (file_exists($path_owncloud_otu . $value)) {
                echo "Have file". $value;
                echo "<br>";
            }else{
                echo "No file". $value;
                echo "<br>";
            }
        }

    }

    public function read_file_otu(){

        $path_file_phylotype = array("final.tx.summary","file_after_reverse.csv","file_phylum_count.txt","final.tx.2.subsample.shared",
            "final.tx.thetayc.2.lt.ave.nmds.axes","final.tx.groups.summary","final.tx.groups.rarefaction","soilpro.pearson.corr.axes");

        $path_file_otu = array("final.opti_mcc.summary","file_after_reverse.csv","file_phylum_count.txt","final.opti_mcc.0.03.subsample.shared",
            "final.opti_mcc.thetayc.0.03.lt.ave.nmds.axes","final.opti_mcc.groups.summary","final.opti_mcc.groups.rarefaction","soilpro.pearson.corr.axes");

        $path_owncloud_phylotype = "owncloud/data/joesoftheart/files/SAMPLE-WES-2023/output/";
        $path_owncloud_otu = "owncloud/data/joesoftheart/files/SAMPLE-OTU/output/";


        $chao_shanon = file_get_contents($path_owncloud_otu."final.opti_mcc.groups.summary");
        $row = explode("\n", $chao_shanon);
        array_shift($row);
        $arr_out = array() ;
        $arr_chao = array();
        $arr_shanon = array();
        $index =0;
        foreach ($row as $value => $data){
            if ($data != null){
                $row_data = preg_split("/\s+/", $data);

                if($row_data[0] == 0.03) {
                    $arr_out[$index] = $row_data[5];
                    $arr_chao[$index] = $row_data[9];
                    $arr_shanon[$index] = $row_data[12];
                    $index++;
                }
            }

        }


        echo "Min otu :" . min($arr_out)."<br>";
        echo "Max otu :" . max($arr_out)."<br>";
        echo "Min chao :" . min($arr_chao)."<br>";
        echo "Max chao :" . max($arr_chao)."<br>";
        echo "Min shanon :" . min($arr_shanon)."<br>";
        echo "Max shanon :" . max($arr_shanon)."<br>";
        echo ">>>>>>>>>>>>>>>>>>>>>End Page 1-2<<<<<<<<<<<<<<<<<br>";


        $rare_index = array();
        $name_sample = array();
        $save_value = array();
        $rarefaction = file_get_contents($path_owncloud_otu."final.opti_mcc.groups.rarefaction");
        $row = explode("\n", $rarefaction);
        $check_index = preg_split('/\s+/', $row[0]);
        for($i=0;$i<count($check_index);$i++){
            $save_index =   explode('-', $check_index[$i]);
            if ($save_index[0] == "0.03") {
                array_push($rare_index, $i);
                array_push($name_sample, $save_index[1]);
            }
        }

        array_shift($row);
        foreach ($row as $value => $data){
            if ($data != null){
                $row_data_rare = preg_split('/\s+/', $data);
                for ($i=0;$i<count($rare_index);$i++){
                    if (is_numeric($row_data_rare[$rare_index[$i]])) {
                        if (isset($save_value[$i])){
                        }else{
                            $save_value[$i] = "0";
                        }
                        if ($save_value[$i] < $row_data_rare[$rare_index[$i]]) {
                            $save_value[$i] = $row_data_rare[$rare_index[$i]];
                        } else {

                        }
                    } else {
                        if ($save_value[$i] == null) {
                            $save_value[$i] = "0";
                        } else {

                        }
                    }
                }
            }
        }
        echo " Name sam :" . $name_sample[0]."  Value :".$save_value[0];
        echo '<br>';
        echo " Name sam :" .$name_sample[1]."  Value :".$save_value[1];
        echo '<br>';
        echo " Name sam :" .$name_sample[2]."  Value :".$save_value[2];
        echo '<br>';
        echo " Name sam :" .$name_sample[3]."  Value :".$save_value[3];
        echo '<br>';
        $name_sample_phylumn = array();
        $value_phylumn =[];
        $save_value_phylumn = array();
        $total_num = null;

        $phylumn_count = file_get_contents($path_owncloud_otu."file_phylum_count.txt");
        $row = explode("\n", $phylumn_count);
        $check_index = preg_split('/\s+/', $row[0]);
        array_shift($row);
        $half_sam = count($check_index)/2;
        for($i=0;$i<count($check_index);$i++){
            $name_sample_phylumn[$i] = $check_index[$i];
        }
        $index = 0;
        foreach ($row as $value => $data){
            $row_data_phy = preg_split('/\s+/', $data);

            if ($data != null) {
                $total_num = null;
                for ($i = 0; $i < count($name_sample_phylumn); $i++) {
                    $value_phylumn[$index][$i] = $row_data_phy[$i] . '<br>';
                    $total_num += $row_data_phy[$i];
                }
                $save_value_phylumn[$index] =  $total_num;
                // echo '<br>';
            }
            $index++;
        }
        $max = 0;
        $k = 0;
        $more = 0;
        $name_phylumn = null;
        foreach ($save_value_phylumn as $key => $value) {

            if ($value >= $max) {
                $max = max($save_value_phylumn);
                $k = $key;
            }
        }
        for ($n = 0;$n<count($name_sample_phylumn);$n++){
            if ($n == 0){
                $name_phylumn = $value_phylumn[$k][$n];
            }
            echo $value_phylumn[$k][$n];
            if ($value_phylumn[$k][$n] > $more){
                $more = $value_phylumn[$k][$n];
            }
        }
        for ($n = 1;$n<count($name_sample_phylumn);$n++){
            echo "Name sample :".$name_sample_phylumn[$n]."  Value :".$value_phylumn[$k][$n].'<br>';
        }
        echo "Big SAM :".$more;
        echo '<br>';

        echo ">>>>>>>>>>>>>>>>>>>>>End Page 3<<<<<<<<<<<<<<<<<br>";
        $genus = file_get_contents($path_owncloud_otu."file_after_reverse.csv");
        $row = explode("\n", $genus);
        $row2 = explode("\n", $genus);
        $row_name = explode("\n", $genus);
        array_shift($row);
        array_shift($row2);
        $count_genus = array();
        $count_genus2 = array();
        $num = 0;
        $key_sam = null;
        $max_for_sam = array();
        foreach ($row as $value => $data) {
            if ($data != null ) {
                $split = preg_split('/,/', $data);
                if ($count_genus == null) {
                    for ($j = 1; $j < count($split); $j++) {
                        $count_genus[$j] =  $split[$j];
                        $count_genus2[$j] =  $split[$j];
                    }
                }else{
                    for ($j = 1; $j < count($split); $j++) {
                        $count_genus[$j] +=  $split[$j];
                        $count_genus2[$j] =  $split[$j];
                    }
                }
                $num = 0;
                $key_index = null;

                for ($k = 1; $k < count($count_genus2); $k++) {
                    if ($num < $count_genus2[$k]){
                        $num = $count_genus2[$k];
                        $key_index = $k;
                    }
                }

                $total = 0;
                foreach ($row2 as $value_e => $data_a){
                    if ($data_a != null ) {
                        $split1 = preg_split('/,/', $data_a);
                        $total += $split1[$key_index];
                    }
                }

                $splitn = preg_split('/,/', $row_name[0]);
                $genus_name = $splitn[$key_index];

                $maxximun = max($count_genus2);
                $percent = $maxximun * 100 / $total;


                echo "SAM :" . $split[0] ."MAX :" . $maxximun  . "INDEX :" . $key_index . "TOTAL : ". $total ."PERCENT :" . round($percent,2)."%" ."GENUS NAME :" . $genus_name;
                echo '<br>';
            }
        }

        $svg_file = file_get_contents($path_owncloud_otu . "final.tx.2.subsample.2.sharedsobs.S1_1_16s_S1-S2_1_16s_S3-S3_1_16s_S5-S4_1_16s_S7.svg");

        $find_string   = '<text';
        $position = strpos($svg_file, $find_string);

        $svg_file_new = substr($svg_file, $position);

        $searchfor = 'The number of species in group';
        $searchfor2 = 'The total richness of all';
        $pattern = preg_quote($searchfor, '/');
// finalise the regular expression, matching the whole line
        $pattern = "/^.*$pattern.*\$/m";
// search, and store all matching occurences in $matches
        if(preg_match_all($pattern, $svg_file, $matches)){

            foreach ($matches[0] as $value => $data_otu){
                $name_sam_otu = preg_split('/\s+/', $data_otu);
                echo $name_sam_otu[11] . " : " . $name_sam_otu[13].'<br>';
            }

        }
        else{
            echo "No matches found";
        }



        $svg_file2 = file_get_contents($path_owncloud_otu . "final.tx.2.subsample.2.sharedsobs.S1_1_16s_S1-S2_1_16s_S3-S3_1_16s_S5-S4_1_16s_S7.sharedotus");
        $row_otu = explode("\n", $svg_file2);
        foreach ($row_otu as $value => $data){
            if ($data != null) {
                $split_row_otu = preg_split('/\s+/', $data);
                $num_outs = $split_row_otu[1];

            }


        }
        echo "Num tou :" . $num_outs.'<br>';

        echo ">>>>>>>>>>>>>>>>>>>>>End Page 4 <<<<<<<<<<<<<<<<<br>";
        $axes1 = array();
        $axes2 = array();
        $near_data = file_get_contents($path_owncloud_otu."final.opti_mcc.thetayc.0.03.lt.ave.nmds.axes");
        $row = explode("\n", $near_data);
        array_shift($row);
        $index = 0;
        foreach ($row as $value => $data) {

            if ($data != null) {
                $split = preg_split('/\s+/', $data);
                $axes1[$index] = $split[1];
                $axes2[$index] = $split[2];
                $index++;
            }
        }

        $sam1 = null;
        $sam2 = null;
        $save_near_county = 0 ;
        for ($i = 0; $i < count($axes2); $i++) {

            for ($j = 0; $j < count($axes2); $j++) {

                if ($i != $j) {
                    if ($save_near_county == null) {
                        $save_near_county =  sqrt(pow((($axes1[$i]) - ($axes1[$j])),2) + pow( (($axes2[$i]) - ($axes2[$j])),2));

                    }else{
                        if ( $save_near_county > sqrt(pow((($axes1[$i]) - ($axes1[$j])),2) + pow((($axes2[$i]) - ($axes2[$j])),2 ))){
                            $save_near_county =  sqrt(pow((($axes1[$i]) - ($axes1[$j])),2) + pow((($axes2[$i]) - ($axes2[$j])),2));
//                            echo ($axes1[$i]) - ($axes1[$j]);
//                            echo '<br>';
//                            echo $axes2[$i] - $axes2[$j];
//                            echo '<br>';
//                            $sam1 =  $i ;
//                            $sam2 = $j;
//                            echo '<br>';
//                            echo $save_near_county;
                        }else{

                        }
                    }
                }
            }

        }

        $index = 0;
        foreach ($row as $value => $data) {

            if ($data != null) {
                $split = preg_split('/\s+/', $data);
                if ($index == $sam1){
                    echo "Sam1". $split[0];
                    echo '<br>';
                }
                if ($index == $sam2){
                    echo "Sam2". $split[0];
                    echo '<br>';
                }
                $index++;
            }
        }

        echo ">>>>>>>>>>>>>>>>>>>>>End Page 5 <<<<<<<<<<<<<<<<<br>";
        echo "In db";
        echo ">>>>>>>>>>>>>>>>>>>>>End Page 6 <<<<<<<<<<<<<<<<<br>";
        echo "In db";
        echo ">>>>>>>>>>>>>>>>>>>>>End Page 7 <<<<<<<<<<<<<<<<<br>";

        foreach ($path_file_phylotype as $value){
            if (file_exists($path_owncloud_phylotype . $value)) {
                echo "Have file". $value;
                echo "<br>";
//                $file = fopen($path_owncloud_phylotype . $value,"r") or die('Unable file');
//                echo fread($file, filesize($path_owncloud_phylotype . $value));
//
//
//
//                fclose($file);

                // echo $file;
            }else{
                echo "No file". $value;
                echo "<br>";
            }
        }
        echo "____________________";
        echo "<br>";
        foreach ($path_file_otu as $value){
            if (file_exists($path_owncloud_otu . $value)) {
                echo "Have file". $value;
                echo "<br>";
            }else{
                echo "No file". $value;
                echo "<br>";
            }
        }

    }
}