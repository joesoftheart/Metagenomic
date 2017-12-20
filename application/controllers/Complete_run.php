<?php
defined('BASEPATH') OR exit("No direct script access allowed");


class Complete_run extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();

    }

    public function index($id_project)
    {
        ob_start();
        $data['rs'] = $this->mongo_db->get_where('projects', array('_id' => new \MongoId($id_project)));
        $project_analysis = null;
        $project_path = null;
        foreach ($data['rs'] as $result) {
            $project_analysis = $result['project_analysis'];
            $project_path = $result['project_path'];

        }


        $project_path = str_replace("../", "", $project_path);
//        echo $project_analysis;
//        echo $project_path;
        $this->load->view('header');
        $this->load->view('complete_run', $data);
        $this->load->view('footer');
        if ($project_analysis == "phylotype") {
            $this->read_file_db_phylotype($project_path, $id_project);

        } else if ($project_analysis == "OTUs") {
            $this->read_file_db_otu($project_path, $id_project);

        }

//


    }

    public function read_file_db_otu($path_owncloud, $id_project)
    {

        $path_file_phylotype = array("final.tx.summary", "file_after_reverse.csv", "file_phylum_count.txt", "final.tx.2.subsample.shared",
            "final.tx.thetayc.2.lt.ave.nmds.axes", "final.tx.groups.summary", "final.tx.groups.rarefaction", "soilpro.pearson.corr.axes");

        $path_file_otu = array("final.opti_mcc.summary", "file_after_reverse.csv", "file_phylum_count.txt", "final.opti_mcc.0.03.subsample.shared",
            "final.opti_mcc.thetayc.0.03.lt.ave.nmds.axes", "final.opti_mcc.groups.summary", "final.opti_mcc.groups.rarefaction", "soilpro.pearson.corr.axes");

        $path_owncloud_phylotype = $path_owncloud . "/output/";
        $path_owncloud_otu = $path_owncloud . "/output/";


        $chao_shanon = file_get_contents($path_owncloud_otu . "final.opti_mcc.groups.summary");
        $row = explode("\n", $chao_shanon);
        array_shift($row);
        $arr_out = array();
        $arr_chao = array();
        $arr_shanon = array();
        $index = 0;
        foreach ($row as $value => $data) {
            if ($data != null) {
                $row_data = preg_split("/\s+/", $data);

                if ($row_data[0] == 0.03) {
                    $arr_out[$index] = $row_data[5];
                    $arr_chao[$index] = $row_data[9];
                    $arr_shanon[$index] = $row_data[12];
                    $index++;
                }
            }

        }


        echo "Min otu :" . min($arr_out) . "<br>";
        echo "Max otu :" . max($arr_out) . "<br>";
        echo "Min chao :" . min($arr_chao) . "<br>";
        echo "Max chao :" . max($arr_chao) . "<br>";
        echo "Min shanon :" . min($arr_shanon) . "<br>";
        echo "Max shanon :" . max($arr_shanon) . "<br>";


        $rare_index = array();
        $name_sample = array();
        $save_value = array();
        $rarefaction = file_get_contents($path_owncloud_otu . "final.opti_mcc.groups.rarefaction");
        $row = explode("\n", $rarefaction);
        $check_index = preg_split('/\s+/', $row[0]);
        for ($i = 0; $i < count($check_index); $i++) {
            $save_index = explode('-', $check_index[$i]);
            if ($save_index[0] == "0.03") {
                array_push($rare_index, $i);
                array_push($name_sample, $save_index[1]);
            }
        }

        array_shift($row);
        foreach ($row as $value => $data) {
            if ($data != null) {
                $row_data_rare = preg_split('/\s+/', $data);
                for ($i = 0; $i < count($rare_index); $i++) {
                    if (is_numeric($row_data_rare[$rare_index[$i]])) {
                        if (isset($save_value[$i])) {
                        } else {
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
//        echo " Name sam :" . $name_sample[0]."  Value :".$save_value[0];
//        echo '<br>';
//        echo " Name sam :" .$name_sample[1]."  Value :".$save_value[1];
//        echo '<br>';
//        echo " Name sam :" .$name_sample[2]."  Value :".$save_value[2];
//        echo '<br>';
//        echo " Name sam :" .$name_sample[3]."  Value :".$save_value[3];
//        echo '<br>';
        $index_hi = null;
        $num_hi = 0;
        for ($i = 0; $i < count($save_value); $i++) {
            if ($num_hi < $save_value[$i]) {
                $num_hi = $save_value[$i];
                $index_hi = $i;
            }
        }
//        echo "Sam :".$name_sample[$index_hi]."Height :".$num_hi."<br>";
        echo "Sample height :" . $sample_height = $name_sample[$index_hi] . '<br>';


        $index_low = null;
        $num_low = 0;
        for ($i = 0; $i < count($save_value); $i++) {
            if ($i == 0) {
                $num_low = $save_value[$i];
                $index_low = $i;
            } else {
                if ($num_low > $save_value[$i]) {
                    $num_low = $save_value[$i];
                    $index_low = $i;

                }
            }
        }
//        echo "Sam :".$name_sample[$index_low]."low :".$num_low;
        echo "Sample low :" . $sample_low = $name_sample[$index_low] . '<br>';
        echo ">>>>>>>>>>>>>>>>>>>>>End Page 1-2<<<<<<<<<<<<<<<<<br>";
        $name_sample_phylumn = array();
        $value_phylumn = [];
        $save_value_phylumn = array();
        $total_num = null;

        $phylumn_count = file_get_contents($path_owncloud_otu . "file_phylum_count.txt");
        $row = explode("\n", $phylumn_count);
        $check_index = preg_split('/\s+/', $row[0]);
        array_shift($row);
        $half_sam = count($check_index) / 2;
        for ($i = 0; $i < count($check_index); $i++) {
            $name_sample_phylumn[$i] = $check_index[$i];
        }
        $index = 0;
        foreach ($row as $value => $data) {
            $row_data_phy = preg_split('/\s+/', $data);

            if ($data != null) {
                $total_num = null;
                for ($i = 0; $i < count($name_sample_phylumn); $i++) {
                    $value_phylumn[$index][$i] = $row_data_phy[$i] . '<br>';
                    $total_num += $row_data_phy[$i];
                }
                $save_value_phylumn[$index] = $total_num;
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
        for ($n = 0; $n < count($name_sample_phylumn); $n++) {
            if ($n == 0) {
                $name_phylumn = $value_phylumn[$k][$n];
            }
            echo $value_phylumn[$k][$n];
            if ($value_phylumn[$k][$n] > $more) {
                $more = $value_phylumn[$k][$n];
            }
        }
        for ($n = 1; $n < count($name_sample_phylumn); $n++) {
            echo "Name sample :" . $name_sample_phylumn[$n] . "  Value :" . $value_phylumn[$k][$n] . '<br>';
        }
        echo "Big SAM :" . $more;
        echo '<br>';

        echo ">>>>>>>>>>>>>>>>>>>>>End Page 3<<<<<<<<<<<<<<<<<br>";
        $genus = file_get_contents($path_owncloud_otu . "file_after_reverse.csv");
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
            if ($data != null) {
                $split = preg_split('/,/', $data);
                if ($count_genus == null) {
                    for ($j = 1; $j < count($split); $j++) {
                        $count_genus[$j] = $split[$j];
                        $count_genus2[$j] = $split[$j];
                    }
                } else {
                    for ($j = 1; $j < count($split); $j++) {
                        $count_genus[$j] += $split[$j];
                        $count_genus2[$j] = $split[$j];
                    }
                }
                $num = 0;
                $key_index = null;

                for ($k = 1; $k < count($count_genus2); $k++) {
                    if ($num < $count_genus2[$k]) {
                        $num = $count_genus2[$k];
                        $key_index = $k;
                    }
                }

                $total = 0;
                foreach ($row2 as $value_e => $data_a) {
                    if ($data_a != null) {
                        $split1 = preg_split('/,/', $data_a);
                        $total += $split1[$key_index];
                    }
                }

                $splitn = preg_split('/,/', $row_name[0]);
                $genus_name = $splitn[$key_index];

                $maxximun = max($count_genus2);
                $percent = $maxximun * 100 / $total;


                echo "SAM :" . $split[0] . "MAX :" . $maxximun . "INDEX :" . $key_index . "TOTAL : " . $total . "PERCENT :" . round($percent, 2) . "%" . "GENUS NAME :" . $genus_name;
                echo '<br>';
            }
        }

        $svg_file = file_get_contents($path_owncloud_otu . "sharedsobs.svg");

        $find_string = '<text';
        $position = strpos($svg_file, $find_string);

        $svg_file_new = substr($svg_file, $position);

        $searchfor = 'The number of species in group';
        $searchfor2 = 'The total richness of all';
        $pattern = preg_quote($searchfor, '/');
// finalise the regular expression, matching the whole line
        $pattern = "/^.*$pattern.*\$/m";
// search, and store all matching occurences in $matches
        if (preg_match_all($pattern, $svg_file, $matches)) {

            foreach ($matches[0] as $value => $data_otu) {
                $name_sam_otu = preg_split('/\s+/', $data_otu);
                echo $name_sam_otu[11] . " : " . $name_sam_otu[13] . '<br>';
            }

        } else {
            echo "No matches found";
        }


        $svg_file2 = file_get_contents($path_owncloud_otu . "sharedsobs.sharedotus");
        $row_otu = explode("\n", $svg_file2);
        foreach ($row_otu as $value => $data) {
            if ($data != null) {
                $split_row_otu = preg_split('/\s+/', $data);
                $num_outs = $split_row_otu[1];

            }


        }
        echo "Num tou :" . $num_outs . '<br>';

        echo ">>>>>>>>>>>>>>>>>>>>>End Page 4 <<<<<<<<<<<<<<<<<br>";
        $axes1 = array();
        $axes2 = array();
        $near_data = file_get_contents($path_owncloud_otu . "final.opti_mcc.thetayc.0.03.lt.ave.nmds.axes");
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
        $save_near_county = 0;
        for ($i = 0; $i < count($axes2); $i++) {

            for ($j = 0; $j < count($axes2); $j++) {

                if ($i != $j) {
                    if ($save_near_county == null) {
                        $save_near_county = sqrt(pow((($axes1[$i]) - ($axes1[$j])), 2) + pow((($axes2[$i]) - ($axes2[$j])), 2));

                    } else {
                        if ($save_near_county > sqrt(pow((($axes1[$i]) - ($axes1[$j])), 2) + pow((($axes2[$i]) - ($axes2[$j])), 2))) {
                            $save_near_county = sqrt(pow((($axes1[$i]) - ($axes1[$j])), 2) + pow((($axes2[$i]) - ($axes2[$j])), 2));
//                            echo ($axes1[$i]) - ($axes1[$j]);
//                            echo '<br>';
//                            echo $axes2[$i] - $axes2[$j];
//                            echo '<br>';
//                            $sam1 =  $i ;
//                            $sam2 = $j;
//                            echo '<br>';
//                            echo $save_near_county;
                        } else {

                        }
                    }
                }
            }

        }

        $index = 0;
        foreach ($row as $value => $data) {

            if ($data != null) {
                $split = preg_split('/\s+/', $data);
                if ($index == $sam1) {
                    echo "Sam1" . $split[0];
                    echo '<br>';
                }
                if ($index == $sam2) {
                    echo "Sam2" . $split[0];
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

        foreach ($path_file_phylotype as $value) {
            if (file_exists($path_owncloud_phylotype . $value)) {
                echo "Have file" . $value;
                echo "<br>";
//                $file = fopen($path_owncloud_phylotype . $value,"r") or die('Unable file');
//                echo fread($file, filesize($path_owncloud_phylotype . $value));
//
//
//
//                fclose($file);

                // echo $file;
            } else {
                echo "No file" . $value;
                echo "<br>";
            }
        }
        echo "____________________";
        echo "<br>";
        foreach ($path_file_otu as $value) {
            if (file_exists($path_owncloud_otu . $value)) {
                echo "Have file" . $value;
                echo "<br>";
            } else {
                echo "No file" . $value;
                echo "<br>";
            }
        }

    }

    public function read_file_db_phylotype($path_owncloud, $id_project)
    {
        $path_file_phylotype = array("final.tx.summary", "file_after_reverse.csv", "file_phylum_count.txt", "final.tx.2.subsample.shared",
            "final.tx.thetayc.2.lt.ave.nmds.axes", "final.tx.groups.summary", "final.tx.groups.rarefaction", "soilpro.pearson.corr.axes");

        $path_file_otu = array("final.opti_mcc.summary", "file_after_reverse.csv", "file_phylum_count.txt", "final.opti_mcc.0.03.subsample.shared",
            "final.opti_mcc.thetayc.0.03.lt.ave.nmds.axes", "final.opti_mcc.groups.summary", "final.opti_mcc.groups.rarefaction", "soilpro.pearson.corr.axes");

        $path_owncloud_phylotype = $path_owncloud . "/output/";
        $path_owncloud_otu = $path_owncloud . "/output/";

        $chao_shanon = file_get_contents($path_owncloud_phylotype . "final.tx.groups.ave-std.summary");
        $row = explode("\n", $chao_shanon);
        array_shift($row);
        $arr_out = array();
        $arr_chao = array();
        $arr_shanon = array();
        $index = 0;
        $table_alpha = array();
        foreach ($row as $value => $data) {
            if ($data != null) {
                $row_data = preg_split("/\s+/", $data);

                if ($row_data[0] == 2 and $row_data[2] == "ave") {
                    $arr_out[$index] = $row_data[5];
                    $arr_chao[$index] = $row_data[9];
                    $arr_shanon[$index] = $row_data[12];
                    $table_alpha[$index] = $row_data[1] . ":" . $row_data[4] . ":" . $row_data[5] . ":" . $row_data[9] . ":" . $row_data[12];
                    $index++;
                }
            }

        }


        $min_otu = min($arr_out);
        $max_otu = max($arr_out);
        $min_chao = min($arr_chao);
        $max_chao = max($arr_chao);
        $min_shanon = min($arr_shanon);
        $max_shanon = max($arr_shanon);
        $t_range_otu = min($arr_out) . "-" . max($arr_out);


        $rare_index = array();
        $name_sample = array();
        $save_value = array();
        $rarefaction = file_get_contents($path_owncloud_phylotype . "final.tx.groups.rarefaction");
        $row = explode("\n", $rarefaction);
        $check_index = preg_split('/\s+/', $row[0]);
        for ($i = 0; $i < count($check_index); $i++) {
            $save_index = explode('-', $check_index[$i]);
            if ($save_index[0] == "2") {
                array_push($rare_index, $i);
                array_push($name_sample, $save_index[1]);
            }
        }


        array_shift($row);
        foreach ($row as $value => $data) {
            if ($data != null) {
                $row_data_rare = preg_split('/\s+/', $data);
                for ($i = 0; $i < count($rare_index); $i++) {
                    if (is_numeric($row_data_rare[$rare_index[$i]])) {
                        if (isset($save_value[$i])) {
                        } else {
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
//        echo " Name sam :" . $name_sample[0]."  Value :".$save_value[0];
//        echo '<br>';
//        echo " Name sam :" .$name_sample[1]."  Value :".$save_value[1];
//        echo '<br>';
//        echo " Name sam :" .$name_sample[2]."  Value :".$save_value[2];
//        echo '<br>';
//        echo " Name sam :" .$name_sample[3]."  Value :".$save_value[3];
//        echo '<br>';

        $index_hi = null;
        $num_hi = 0;
        for ($i = 0; $i < count($save_value); $i++) {
            if ($num_hi < $save_value[$i]) {
                $num_hi = $save_value[$i];
                $index_hi = $i;
            }
        }
//        echo "Sam :".$name_sample[$index_hi]."Height :".$num_hi."<br>";
        $sample_height = $name_sample[$index_hi];


        $index_low = null;
        $num_low = 0;
        for ($i = 0; $i < count($save_value); $i++) {
            if ($i == 0) {
                $num_low = $save_value[$i];
                $index_low = $i;
            } else {
                if ($num_low > $save_value[$i]) {
                    $num_low = $save_value[$i];
                    $index_low = $i;

                }
            }
        }
//        echo "Sam :".$name_sample[$index_low]."low :".$num_low;
        $sample_low = $name_sample[$index_low];


        $read_logs = file_get_contents($path_owncloud_phylotype . "database.txt");
        $row = explode("\n", $read_logs);


        $index = 0;
        $count_seqs = null;
        $avg_lenght = null;
        $num_seqs = null;
        $avg_reads = null;
        foreach ($row as $value => $data) {
            if ($data != null) {
                $row_data = preg_split("/:/", $data);

                if ($index == 0) {
                    $count_seqs = $row_data[1];
                } else if ($index == 1) {
                    $avg_lenght = $row_data[1];
                } else if ($index == 2) {
                    $num_seqs = $row_data[1];
                } else if ($index == 3) {
                    $avg_reads = $row_data[1];
                }
            }
            $index++;
        }

//        echo ">>>>>>>>>>>>>>>>>>>>>End Page 1-2<<<<<<<<<<<<<<<<<br>";
//        echo "Bigsam_rare :". $sample_height.'<br>';
        $sample_big_rare = $sample_height;
        $name_sample_phylumn = array();
        $value_phylumn = [];
        $save_value_phylumn = array();
        $total_num = null;

        $phylumn_count = file_get_contents($path_owncloud_phylotype . "file_phylum_count.txt");
        $row = explode("\n", $phylumn_count);
        $check_index = preg_split('/\s+/', $row[0]);
        array_shift($row);
        $half_sam = count($check_index) / 2;
        for ($i = 0; $i < count($check_index); $i++) {
            $name_sample_phylumn[$i] = $check_index[$i];
        }
        $index = 0;
        foreach ($row as $value => $data) {
            $row_data_phy = preg_split('/\s+/', $data);

            if ($data != null) {
                $total_num = null;
                for ($i = 0; $i < count($name_sample_phylumn); $i++) {
                    $value_phylumn[$index][$i] = $row_data_phy[$i];
                    $total_num += $row_data_phy[$i];
                }
                $save_value_phylumn[$index] = $total_num;
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
        for ($n = 0; $n < count($name_sample_phylumn); $n++) {
            if ($n == 0) {
                $name_phylumn = $value_phylumn[$k][$n];
                $sample_big_phy = $name_sample_phylumn[$n];
            }

            if ($value_phylumn[$k][$n] > $more) {
                $more = $value_phylumn[$k][$n];
                $sample_big_phy = $name_sample_phylumn[$n];
            }
        }
        for ($n = 1; $n < count($name_sample_phylumn); $n++) {


        }
//        echo "Name phylumn :" . $name_phylumn.'<br>';
//        echo "Big SAM :".$more . "Name Sam :" . $sample_big_phy ;
//        echo '<br>';

//        echo ">>>>>>>>>>>>>>>>>>>>>End Page 3<<<<<<<<<<<<<<<<<br>";
        $genus = file_get_contents($path_owncloud_phylotype . "file_after_reverse.csv");
        $row = explode("\n", $genus);
        $row2 = explode("\n", $genus);
        $row_name = explode("\n", $genus);
        array_shift($row);
        array_shift($row2);
        $count_genus = array();
        $count_genus2 = array();
        $num = 0;
        $key_sam = null;
        $genus_pack = array();
        $max_for_sam = array();
        $index_genus = 0;
        $name_sample_num_ven = array();
        $index_no_unc = array();
        $split_index = preg_split('/,/', $row_name[0]);

        foreach ($row as $value => $data) {
            if ($data != null) {
                $split = preg_split('/,/', $data);
                if ($count_genus == null) {
                    for ($j = 1; $j < count($split); $j++) {
                        $count_genus[$j] = $split[$j];
                        $count_genus2[$j] = $split[$j];
                    }
                } else {
                    for ($j = 1; $j < count($split); $j++) {
                        $count_genus[$j] += $split[$j];
                        $count_genus2[$j] = $split[$j];
                    }
                }
                $num = 0;
                $key_index = null;

                for ($k = 1; $k < count($count_genus2); $k++) {
                    if ($num < $count_genus2[$k]) {
                        $num = $count_genus2[$k];
                        $key_index = $k;
                    }
                }

                $total = 0;
                foreach ($row2 as $value_e => $data_a) {
                    if ($data_a != null) {
                        $split1 = preg_split('/,/', $data_a);
                        $total += $split1[$key_index];
                    }
                }

                $splitn = preg_split('/,/', $row_name[0]);
                $genus_name = $splitn[$key_index];

                $maxximun = max($count_genus2);
                $percent = $maxximun * 100 / $total;


                $genus_pack[$index_genus] = $split[0] . ":" . $maxximun . ":" . $key_index . ":" . $total . ":" . round($percent, 2) . "%" . ":" . $genus_name;

                $index_genus++;
            }
        }
        $abun_genus = $genus_pack;
        $svg_file = file_get_contents($path_owncloud_phylotype . "sharedsobs.svg");
        // $svg_file = file_get_contents($path_owncloud_phylotype . "sharedsobs.svg");
        $find_string = '<text';
        $position = strpos($svg_file, $find_string);

        $svg_file_new = substr($svg_file, $position);

        $searchfor = 'The number of species in group';
        $searchfor2 = 'The total richness of all';
        $pattern = preg_quote($searchfor, '/');
// finalise the regular expression, matching the whole line
        $pattern = "/^.*$pattern.*\$/m";
// search, and store all matching occurences in $matches
        if (preg_match_all($pattern, $svg_file, $matches)) {
            $index_ven = 0;
            foreach ($matches[0] as $value => $data_otu) {
                $name_sam_otu = preg_split('/\s+/', $data_otu);
                $repalce_str = str_replace('</text>', '', $name_sam_otu[13]);
//                echo $name_sam_otu[11] . " : " . $repalce_str;
                $name_sample_num_ven[$index_ven] = $name_sam_otu[11] . " : " . $repalce_str;
//                echo $repalce_str;
                $index_ven++;
            }

        } else {
            echo "No matches found";
        }


        $svg_file2 = file_get_contents($path_owncloud_phylotype . "sharedsobs.sharedotus");
//        $svg_file2 = file_get_contents($path_owncloud_phylotype . "sharedotus.sharedotus");
        $row_otu = explode("\n", $svg_file2);
        foreach ($row_otu as $value => $data) {
            if ($data != null) {
                $split_row_otu = preg_split('/\s+/', $data);
                $num_outs = $split_row_otu[1];

            }


        }
//        echo "Num tou :" . $num_outs.'<br>';
        $num_otu = $num_outs;
//        echo ">>>>>>>>>>>>>>>>>>>>>End Page 4 <<<<<<<<<<<<<<<<<br>";
        $axes1 = array();
        $axes2 = array();
        $near_data = file_get_contents($path_owncloud_phylotype . "final.tx.thetayc.2.lt.ave.nmds.axes");
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
        $save_near_county = 0;
        for ($i = 0; $i < count($axes2); $i++) {

            for ($j = 0; $j < count($axes2); $j++) {

                if ($i != $j) {
                    if ($save_near_county == null) {
                        $save_near_county = sqrt(pow((($axes1[$i]) - ($axes1[$j])), 2) + pow((($axes2[$i]) - ($axes2[$j])), 2));
                        $sam1 = $i;
                        $sam2 = $j;

                    } else if ($save_near_county > sqrt(pow((($axes1[$i]) - ($axes1[$j])), 2) + pow((($axes2[$i]) - ($axes2[$j])), 2))) {
                        $save_near_county = sqrt(pow((($axes1[$i]) - ($axes1[$j])), 2) + pow((($axes2[$i]) - ($axes2[$j])), 2));

                        $sam1 = $i;
                        $sam2 = $j;
                    } else {

                    }

                }
            }

        }
        $near_sam = "";
        $index = 0;
        foreach ($row as $value => $data) {

            if ($data != null) {
                $split = preg_split('/\s+/', $data);
                if ($index == $sam1) {
                    // echo "Sam1". $split[0];
                    //echo '<br>';

                    $near_sam1 = $split[0];
                }
                if ($index == $sam2) {
                    //echo "Sam2". $split[0];
                    //echo '<br>';
                    $near_sam2 = $split[0];
                }
                $index++;
            }
        }
        $near_sam = $near_sam1 . ":" . $near_sam2;


        $table_stat = file_get_contents($path_owncloud_phylotype . "final.tx.summary");
        $row = explode("\n", $table_stat);
        array_shift($row);
        $arr_sam1 = array();
        $arr_sam2 = array();
        $arr_lennon = array();
        $arr_jclass = array();
        $arr_morisitahorn = array();
        $arr_sorabund = array();
        $arr_theten = array();
        $arr_thetayc = array();
        $arr_braycurtis = array();

        $index = 0;
        $table_stat = array();
        foreach ($row as $value => $data) {
            if ($data != null) {
                $row_data = preg_split("/\s+/", $data);

                if ($row_data[0] == 2) {
                    $arr_sam1[$index] = $row_data[1];
                    $arr_sam2[$index] = $row_data[2];
                    $arr_lennon[$index] = $row_data[3];
                    $arr_jclass[$index] = $row_data[4];
                    $arr_morisitahorn[$index] = $row_data[5];
                    $arr_sorabund[$index] = $row_data[6];
                    $arr_theten[$index] = $row_data[7];
                    $arr_thetayc[$index] = $row_data[8];
                    $arr_braycurtis[$index] = $row_data[9];
                    $table_stat[$index] = $row_data[1] . ":" . $row_data[2] . ":" . $row_data[3] . ":" . $row_data[4] . ":" . $row_data[5] . ":" . $row_data[6] . ":" . $row_data[7] . ":" . $row_data[8] . ":" . $row_data[9];
                    $index++;
                }
            }

        }


        $amova = file_get_contents($path_owncloud_phylotype . "final.tx.thetayc.2.lt.ave.amova");
        $row = explode("\n", $amova);


        $index = 0;
        $amova = array();
        $name_vs_sam = array();
        $p_value = array();
        foreach ($row as $value => $data) {
            if ($data != null) {
                $row_data = preg_split("/\s+/", $data);

                if ($index == 0) {


                    $name_vs_sam = array_push($name_vs_sam, $row_data[0]);
                    //                   $table_stat[$index] = $row_data[1] . ":" . $row_data[2] . ":" . $row_data[3] . ":" . $row_data[4] . ":" . $row_data[5] . ":" . $row_data[6] . ":" . $row_data[7] . ":" . $row_data[8] . ":" . $row_data[9];
//
                } else if ($index % 7 == 0) {
                    if ($name_vs_sam == null) {
                        $name_vs_sam = $row_data[0];
                    } else {
                        $name_vs_sam = $name_vs_sam . ":" . $row_data[0];
                    }
                    //  $name_vs_sam = array_push($name_vs_sam, $row_data[0]);
                }

                if ($row_data[0] == "p-value:") {
                    if ($p_value == null) {
                        $p_value = $row_data[1];
                    } else {
                        $p_value = $p_value . ":" . $row_data[1];
                    }
                    // $p_value = array_push($p_value, $row_data[1]);;
                }

            }
            $index++;
        }


        $homova = file_get_contents($path_owncloud_phylotype . "final.tx.thetayc.2.lt.ave.homova");
        $row = explode("\n", $homova);


        $index = 0;
        $name_vs_sam_homo = null;
        $p_value_homo = null;
        $split_homova = preg_split('/\s+/', $row[1]);
        $name_vs_sam_homo = $split_homova[0];
        $p_value_homo = $split_homova[2];


//
//        echo ">>>>>>>>>>>>>>>>>>>>>End Page 5 <<<<<<<<<<<<<<<<<br>";
//        echo "In db";
//        echo ">>>>>>>>>>>>>>>>>>>>>End Page 6 <<<<<<<<<<<<<<<<<br>";
//        echo "In db";
//        echo ">>>>>>>>>>>>>>>>>>>>>End Page 7 <<<<<<<<<<<<<<<<<br>";


        $data = array("avg_read_pre" => "",
            "num_seqs" => "",
            "t_range_otu" => $t_range_otu,
            "lib_size" => "",
            "min_otu" => $min_otu,
            "max_otu" => $max_otu,
            "max_chao" => $max_chao,
            "min_chao" => $min_chao,
            "max_shanon" => $max_shanon,
            "min_shanon" => $min_shanon,
            "sample_hi" => $sample_height,
            "sample_low" => $sample_low,
            "sample_big_rare" => $sample_big_rare,
            "name_phylumn" => $name_phylumn,
            "common_sample_phylumn" => $name_sample_phylumn,
            "sample_big_phy" => $sample_big_phy,
            "abun_genus" => $abun_genus,
            "name_sample_num_ven" => $name_sample_num_ven,
            "num_otu" => $num_otu,
            "near_sam" => $near_sam,
            "table_alpha" => $table_alpha,
            "table_stat" => $table_stat,
            "name_vs_sam" => $name_vs_sam,
            "p_value" => $p_value,
            "name_vs_sam_homo" => $name_vs_sam_homo,
            "p_value_homo" => $p_value_homo,
            "count_seqs" => $count_seqs,
            "avg_lenght" => $avg_lenght,
            "num_seqs2" => $num_seqs,
            "avg_reads" => $avg_reads


        );

        $this->mongo_db->where(array('project_id' => $id_project))->set($data)->update('projects_run');
//
//


        foreach ($path_file_phylotype as $value) {
            if (file_exists($path_owncloud_phylotype . $value)) {
//                echo "Have file". $value;
//                echo "<br>";
//                $file = fopen($path_owncloud_phylotype . $value,"r") or die('Unable file');
//                echo fread($file, filesize($path_owncloud_phylotype . $value));
//
//
//
//                fclose($file);

                // echo $file;
            } else {
//                echo "No file". $value;
//                echo "<br>";
            }
        }
//        echo "____________________";
//        echo "<br>";
        foreach ($path_file_phylotype as $value) {
//            if (file_exists($path_owncloud_otu . $value)) {
//                echo "Have file". $value;
//                echo "<br>";
//            }else{
//                echo "No file". $value;
//                echo "<br>";
//            }
        }

    }

    function on_check_remove_progress($id_project)
    {
        $data = $this->mongo_db->get_where('projects', array('_id' => new \MongoId($id_project)));

        foreach ($data as $value) {
            $a = $value['project_path'];

        }

        echo "on_check_remove" . "\n";
        $path_dir = $a . "/output/";
        if (is_dir($path_dir)) {
            if ($read = opendir($path_dir)) {
                while (($file = readdir($read)) !== false) {

                    $allowed = array('txt');
                    $ext = pathinfo($file, PATHINFO_EXTENSION);

                    if (in_array($ext, $allowed)) {

                        unlink($path_dir . $file);
                    }
                }

                closedir($read);
                redirect('projects/index/' . $id_project, 'refresh');
            }
        }


    }


}









/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/10/17
 * Time: 2:10 PM
 */