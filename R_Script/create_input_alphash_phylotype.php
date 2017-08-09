<?php

//set_time_limit(60);
$user = $argv[1];
$project = $argv[2];
$path_file_original = "../owncloud/data/$user/files/$project/output/final.tx.groups.ave-std.summary";
$file_after_chao = "owncloud/data/$user/files/$project/output/file_after_chao.txt";

if ($file_original = fopen($path_file_original, "r")) {
    $keywords_split_line = preg_split("/[\n]/", fread($file_original, filesize($path_file_original)));


    $num_line = count($keywords_split_line);

    if (file_exists($file_after_chao)) {
        file_put_contents($file_after_chao, "");
    }
    if (filesize($file_after_chao) == 0){
        file_put_contents($file_after_chao, "result" . "\t" . "value" . "\t" . "Source" . "\n", FILE_APPEND);
    }


    for ($i = 0; $i <= $num_line - 1; $i++) {
        $line_split_tab_i = preg_split("/[\t]/", $keywords_split_line[$i]);
        if ($line_split_tab_i[0] == "2" and $line_split_tab_i[2] == "ave"){
            file_put_contents($file_after_chao, "Chao" . "\t" . $line_split_tab_i[11] . "\t" . $line_split_tab_i[1] . "\n", FILE_APPEND);
            file_put_contents($file_after_chao, "Chao" . "\t" . $line_split_tab_i[9] . "\t" . $line_split_tab_i[1] . "\n", FILE_APPEND);
            file_put_contents($file_after_chao, "Chao" . "\t" . $line_split_tab_i[10] . "\t" . $line_split_tab_i[1] . "\n", FILE_APPEND);

        }

    }
    for ($i = 0; $i <= $num_line - 1; $i++) {
        $line_split_tab_i = preg_split("/[\t]/", $keywords_split_line[$i]);
        if ($line_split_tab_i[0] == "2" and $line_split_tab_i[2] == "ave"){
            file_put_contents($file_after_chao, "Shannon" . "\t" . $line_split_tab_i[14] . "\t" . $line_split_tab_i[1] . "\n", FILE_APPEND);
            file_put_contents($file_after_chao, "Shannon" . "\t" . $line_split_tab_i[12] . "\t" . $line_split_tab_i[1] . "\n", FILE_APPEND);
            file_put_contents($file_after_chao, "Shannon" . "\t" . $line_split_tab_i[13] . "\t" . $line_split_tab_i[1] . "\n", FILE_APPEND);

        }

    }


}

?>