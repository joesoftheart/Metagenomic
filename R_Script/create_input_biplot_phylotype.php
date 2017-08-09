<?php
//set_time_limit(60);
$user = $argv[1];
$project = $argv[2];
$file_subsample = "../owncloud/data/$user/files/$project/output/final.tx.2.subsample.spearman.corr.axes";
$file_taxonomy = "../owncloud/data/$user/files/$project/output/final.tx.2.cons.taxonomy";
$file_output_bioplot = "../owncloud/data/$user/files/$project/output/output_bioplot.txt";




if(file_exists($file_output_bioplot)){
    file_put_contents($file_output_bioplot, "");
}



if (file_exists($file_subsample) and file_exists($file_taxonomy)){
    if ($file = fopen($file_subsample, "r") and $file2 = fopen($file_taxonomy,'r')) {
        $keywords_split_line_sample = preg_split("/[\n]/", fread($file, filesize($file_subsample)));
        $keywords_split_line_tax = preg_split("/[\n]/", fread($file2, filesize($file_taxonomy)));
        for ($i=0; $i <= 10; $i++) {
            if ($i == 0){

                file_put_contents($file_output_bioplot, $keywords_split_line_sample[$i] . "\t" . "taxon" . "\n", FILE_APPEND);
                echo $keywords_split_line_sample[$i] . "\t" . "taxon";
                echo "<br/>";
            }else {
                $k = preg_split("/[\t]/", $keywords_split_line_tax[$i]);
                file_put_contents($file_output_bioplot, $keywords_split_line_sample[$i] . "\t" . $k[2] . "\n", FILE_APPEND);
                echo $keywords_split_line_sample[$i] . "\t" . $k[2];
                echo "<br/>";
            }

        }
    }
}







?>