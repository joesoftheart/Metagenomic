<?php
set_time_limit(60);
$user = $argv[1];
$project = $argv[2];
$path_file_original = $argv[3];

$file_index = "owncloud/data/$user/files/$project/output/file_index.txt";
$file_before_reverse = "owncloud/data/$user/files/$project/output/file_before_reverse.txt";
$file_after_reverse = "owncloud/data/$user/files/$project/output/file_after_reverse.csv";

if (file_exists($file_before_reverse)) {
    file_put_contents($file_before_reverse, '');
}

if ($file_original = fopen($path_file_original, "r")) {
    $keywords_split_line = preg_split("/[\n]/", fread($file_original, filesize($path_file_original)));
    $save_index = array();
    $line_split_out1 = null;
    $line_split_out2 = null;
    $line_split_out3 = null;
    $line_split_out4 = null;
    $line_split_out5 = null;

    $num_line = count($keywords_split_line);
    for ($i = 0; $i <= $num_line - 2; $i++) {
        $line_split_tab_i = preg_split("/[\t]/", $keywords_split_line[$i]);
        if ($line_split_tab_i[0] == "taxonomy") {
            $save_index[$i] = $line_split_tab_i[0];
        } else if ($line_split_tab_i[0] == "Root") {
            $save_index[$i] = $line_split_tab_i[0];
        } else {
            $name_index_i = preg_split("/[;]/", $line_split_tab_i[0]);
            $save_index[$i] = $name_index_i[6];
        }
    }

    $array = array_unique(array_merge($save_index));
    end($array);
    $key = key($array);

    if (file_exists($file_index)) {
        file_put_contents($file_index, "");
    }


    for ($j = 0; $j <= $key; $j++) {
        if (isset($array[$j])) {
            file_put_contents($file_index, $array[$j] . "\n", FILE_APPEND);
        }
    }


    if ($file_i = fopen($file_index, "r")) {
        $keywords_index_line = preg_split("/[\n]/", fread($file_i, filesize($file_index)));
        $file_ori = fopen($path_file_original, "r");
        $keywords_ori_split_line = preg_split("/[\n]/", fread($file_ori, filesize($path_file_original)));
        for ($o = 0; $o < count($keywords_index_line); $o++) {
            if ($keywords_index_line[$o] == "taxonomy") {
                for ($k = 0; $k <= $num_line - 1; $k++) {
                    $line_split_k = preg_split("/[\t]/", $keywords_ori_split_line[$k]);
                    if (isset($keywords_index_line[$o])) {
                        if ($keywords_index_line[$o] == $line_split_k[0]) {
//                    echo $keywords_o[$o] . "\t" . $line_split_k[2] . "\t" . $line_split_k[3] . "\t" . $line_split_k[4] . "\t" . $line_split_k[5];
                            $line_split_out1 = $keywords_index_line[$o];
                            $line_split_out2 = $line_split_k[2];
                            $line_split_out3 = $line_split_k[3];
                            $line_split_out4 = $line_split_k[4];
                            $line_split_out5 = $line_split_k[5];
                        }
                    }

                }
            } else if ($keywords_index_line[$o] == "Root") {
                $line_split_out1 = null;
                $line_split_out2 = null;
                $line_split_out3 = null;
                $line_split_out4 = null;
                $line_split_out5 = null;

            } else {
                for ($k = 2; $k <= $num_line - 2; $k++) {
                    $line_split_k = preg_split("/[\t]/", $keywords_ori_split_line[$k]);
                    $name_index_k = preg_split("/[;]/", $line_split_k[0]);
                    if (isset($keywords_index_line[$o])) {
                        if ($keywords_index_line[$o] == $name_index_k[6]) {
//                    echo $keywords_o[$o] . "\t" . $line_split_k[2] . "\t" . $line_split_k[3] . "\t" . $line_split_k[4] . "\t" . $line_split_k[5];

                            $line_split_out1 = $keywords_index_line[$o];
                            $line_split_out2 = $line_split_out2 + $line_split_k[2];
                            $line_split_out3 = $line_split_out3 + $line_split_k[3];
                            $line_split_out4 = $line_split_out4 + $line_split_k[4];
                            $line_split_out5 = $line_split_out5 + $line_split_k[5];
                        }
                    }
                }
            }
            if ($keywords_index_line[$o] != "Root" and $line_split_out1 != null) {
                // echo $line_split_out1 . "\t" . $line_split_out2 . "\t" . $line_split_out3 . "\t" . $line_split_out4 . "\t" . $line_split_out5;
                file_put_contents($file_before_reverse, $line_split_out1 . "\t" . $line_split_out2 . "\t" . $line_split_out3 . "\t" . $line_split_out4 . "\t" . $line_split_out5 . "\n", FILE_APPEND);

                $line_split_out1 = null;
                $line_split_out2 = null;
                $line_split_out3 = null;
                $line_split_out4 = null;
                $line_split_out5 = null;
            }
        }
    }
}

if ($file_cs = fopen($file_before_reverse, "r")) {
    $keywords_last = preg_split("/[\n]/", fread($file_cs, filesize($file_before_reverse)));
    $line_num = preg_split("/[\t]/", $keywords_last[0]);
    file_put_contents($file_after_reverse, "");
    for ($f = 0; $f <= count($line_num) - 1; $f++) {
        for ($j = 0; $j < count($keywords_last) - 1; $j++) {
            $str = str_replace("\r", '', $keywords_last[$j]);
            $line_split_num = preg_split("/[\t]/", $str);
            $strt = str_replace("\r", '', $line_split_num[$f]);
            if ($j == count($keywords_last) - 2) {
                file_put_contents($file_after_reverse, $strt, FILE_APPEND);
            } else {

                file_put_contents($file_after_reverse, $strt . ",", FILE_APPEND);
            }
        }


        file_put_contents($file_after_reverse, "\n", FILE_APPEND);
    }
    echo "complete file_after_reverse.csv";
}
?>