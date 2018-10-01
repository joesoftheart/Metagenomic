<?php

$path_data = $argv[1];
$numcheck  = $argv[2];
$check_pcoa_or_nmds  = $argv[3];



  $xlab = "";
  $ylab = "";

  if($check_pcoa_or_nmds == "pcoa"){

      $xlab = "PC1";
      $ylab = "PC2";

  }else if($check_pcoa_or_nmds == "nmds"){

      $xlab = "Axis 1";
      $ylab = "Axis 2";

  }


echo $numcheck."\n";

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
else if($numcheck == "3otu") {
   
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

    //echo $path_data."\n";
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


function get_Biplot($file_pcoa_or_nmds, $path_output_biplot_otu, $path_biplot_txt, $path_output_biplot_Metadata, $path_input_file_metadata)
{
    
    # get args 7
    $cmd = "/usr/bin/Rscript  R_Script/Biplot_graph_otu.R $file_pcoa_or_nmds $path_output_biplot_otu $path_biplot_txt $path_output_biplot_Metadata $path_input_file_metadata $xlab $ylab";
    exec($cmd);

}

function get_Biplot_Metadata($file_pcoa_or_nmds,$path_output_biplot_Metadata, $path_input_file_metadata){

    # get args 5
    $cmd = "/usr/bin/Rscript  R_Script/Biplot_graph_Metadata_advance.R $file_pcoa_or_nmds $path_output_biplot_Metadata $path_input_file_metadata $xlab $ylab";
    exec($cmd);

}


function get_Biplot_Otu($file_pcoa_or_nmds, $path_output_biplot_otu, $path_biplot_txt){

    # get args 5
    $cmd = "/usr/bin/Rscript  R_Script/Biplot_graph_otu_advance.R $file_pcoa_or_nmds $path_output_biplot_otu $path_biplot_txt $xlab $ylab";
    exec($cmd);

}


?>