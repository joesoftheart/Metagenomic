<?php

$user = $argv[1];
$project = $argv[2];

$level = $argv[3];
$method_otu = $argv[4];
$calculator = $argv[5];


$val_name_cal = explode(",", $calculator);
echo sizeof($val_name_cal) . "\n";

for ($i = 0; $i < sizeof($val_name_cal); $i++) {

    echo $val_name_cal[$i] . "\n";

    $file_output_biplot = "owncloud/data/$user/files/$project/output/output_biplot_" . $val_name_cal[$i] . ".txt";

    file_put_contents($file_output_biplot, "");

    $file_subsample = "owncloud/data/$user/files/$project/output/final.tx." . $level . ".subsample." . $method_otu . ".corr.axes_" . $val_name_cal[$i];

    $file_taxonomy = "owncloud/data/$user/files/$project/output/final.tx." . $level . ".cons.taxonomy";


    create_file_output_biplot($file_subsample, $file_taxonomy, $file_output_biplot);

}


function create_file_output_biplot($file_subsample, $file_taxonomy, $file_output_biplot)
{

    if ($file = fopen($file_subsample, "r") and $file2 = fopen($file_taxonomy, 'r')) {

        $keywords_split_line_sample = preg_split("/[\n]/", fread($file, filesize($file_subsample)));
        $keywords_split_line_tax = preg_split("/[\n]/", fread($file2, filesize($file_taxonomy)));

        for ($i = 0; $i <= 10; $i++) {
            if ($i == 0) {

                file_put_contents($file_output_biplot, $keywords_split_line_sample[$i] . "\t" . "taxon" . "\n", FILE_APPEND);

            } else {
                $k = preg_split("/[\t]/", $keywords_split_line_tax[$i]);
                file_put_contents($file_output_biplot, $keywords_split_line_sample[$i] . "\t" . $k[2] . "\n", FILE_APPEND);

            }

        }
    }

}


?>