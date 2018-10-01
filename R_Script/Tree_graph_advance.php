<?php

$data_tree = $argv[1];
$level = $argv[2];
$user = $argv[3];
$project = $argv[4];
$project_save = $argv[5];

$size_data = explode(",", $data_tree);

for ($i = 0; $i < count($size_data) - 1; $i++) {

    $val_tree = explode("-", $size_data[$i]);

    $path_input_tree = "owncloud/data/$user/files/$project/output/final.tx." . $val_tree[0] . "." . $level . ".lt.ave.tre";
    $path_output_tree = "data_report_mothur/$user/$project_save/beta_diversity_analysis/Tree_" . $val_tree[0] . ".svg";
    get_TreeGraph($path_input_tree, $path_output_tree);
}


function get_TreeGraph($path_input_tree, $path_output_tree)
{

    $cmd = "/usr/bin/Rscript  R_Script/Tree_graph.R $path_input_tree $path_output_tree";
    exec($cmd);

}


?>