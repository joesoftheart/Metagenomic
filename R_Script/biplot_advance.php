<?php

$path_data = $argv[1];
$numcheck  = $argv[2];

#correlation metadata
if($numcheck == "3meta"){

    $size_data = explode(",", $path_data);

    for ($i = 0; $i < count($size_data) - 1; $i++) {

         $val_biplot = explode("-", $size_data[$i]);
         
            $file_pcoa_or_nmds = $val_biplot[0];
            $path_output_biplot_Metadata = $val_biplot[1];
            $path_input_file_metadata = $val_biplot[2];

     get_Biplot_Metadata($file_pcoa_or_nmds,$path_output_biplot_Metadata, $path_input_file_metadata);
    }
}

#correlation metadata
elseif ($numcheck == "3otu") {
   
    $size_data = explode(",", $path_data);

    for ($i = 0; $i < count($size_data) - 1; $i++) {

         $val_biplot = explode("-", $size_data[$i]);

          $file_pcoa_or_nmds = $val_biplot[0];
          $path_output_biplot_otu = $val_biplot[1];
          $path_biplot_txt = $val_biplot[2];

    get_Biplot_Otu($file_pcoa_or_nmds, $path_output_biplot_otu, $path_biplot_txt);
  
   }
}


#all correlation
else{

    $size_data = explode(",", $path_data);

    for ($i = 0; $i < count($size_data) - 1; $i++) {

         $val_biplot = explode("-", $size_data[$i]);

          $file_pcoa_or_nmds = $val_biplot[0];
          $path_output_biplot_otu = $val_biplot[1];
          $path_biplot_txt = $val_biplot[2];
          $path_output_biplot_Metadata = $val_biplot[3];
          $path_input_file_metadata = $val_biplot[4];

     get_Biplot($file_pcoa_or_nmds, $path_output_biplot_otu, $path_biplot_txt, $path_output_biplot_Metadata, $path_input_file_metadata);
   }

}


function get_Biplot($file_pcoa_or_nmds, $path_output_biplot_otu, $path_biplot_txt, $path_output_biplot_Metadata, $path_input_file_metadata){

    $cmd = "/usr/bin/Rscript  R_Script/Biplot_graph_phylotype.R $file_pcoa_or_nmds $path_output_biplot_otu $path_biplot_txt $path_output_biplot_Metadata $path_input_file_metadata";
    exec($cmd);

}


function get_Biplot_Metadata($file_pcoa_or_nmds,$path_output_biplot_Metadata, $path_input_file_metadata){

    $cmd = "/usr/bin/Rscript  R_Script/Biplot_graph_Metadata_advance.R $file_pcoa_or_nmds $path_output_biplot_Metadata $path_input_file_metadata";
    exec($cmd);

}


function get_Biplot_Otu($file_pcoa_or_nmds, $path_output_biplot_otu, $path_biplot_txt){

    $cmd = "/usr/bin/Rscript  R_Script/Biplot_graph_otu_advance.R $file_pcoa_or_nmds $path_output_biplot_otu $path_biplot_txt";
    exec($cmd);

}


?>