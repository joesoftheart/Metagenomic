<?php 

$path_data = $argv[1];

$size_data = explode(",", $path_data);
        
for($i=0; $i < count($size_data)-1 ; $i++) {
             
      $val_biplot = explode("-", $size_data[$i]);

      $file_pcoa_or_nmds = $val_biplot[0];
      $path_output_biplot_otu = $val_biplot[1];
      $path_biplot_txt = $val_biplot[2];
      $path_output_biplot_Metadata = $val_biplot[3];
      $path_input_file_metadata  = $val_biplot[4];

      get_Biplot($file_pcoa_or_nmds,$path_output_biplot_otu,$path_biplot_txt,$path_output_biplot_Metadata,$path_input_file_metadata);
      #echo $file_pcoa_or_nmds."\n".$path_output_biplot_otu."\n".$path_biplot_txt."\n".$path_output_biplot_Metadata."\n".$path_input_file_metadata."\n";
}


function get_Biplot($file_pcoa_or_nmds,$path_output_biplot_otu,$path_biplot_txt,$path_output_biplot_Metadata,$path_input_file_metadata){

	 $cmd = "/usr/bin/Rscript  R_Script/Biplot_graph_phylotype.R $file_pcoa_or_nmds $path_output_biplot_otu $path_biplot_txt $path_output_biplot_Metadata $path_input_file_metadata";
     exec($cmd);

}


 ?>