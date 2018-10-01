<?php

	$data = $argv[1];
	$data_check = $argv[2];


	$value_data = explode(",", $data);

	if($data_check == "pcoa"){

		for ($i = 0; $i < sizeof($value_data); $i++) {

    		$value_data2 = explode("-", $value_data[$i]);
    		$file = $value_data2[0];
    		$image = $value_data2[1];
    		get_file_image_pcoa($file, $image);
		}

	}else if($data_check == "nmds"){

		for ($i = 0; $i < sizeof($value_data); $i++) {

    		$value_data2 = explode("-", $value_data[$i]);
    		$file = $value_data2[0];
    		$image = $value_data2[1];
    		get_file_image_nmds($file, $image);
		}

	}


	function get_file_image_pcoa($get_file, $get_img){

         $cmd = "/usr/bin/Rscript  R_Script/PCoA_graph.R $get_file $get_img";
         exec($cmd);

     }

    function get_file_image_nmds($get_file, $get_img){

         $cmd = "/usr/bin/Rscript  R_Script/NMD_graph.R $get_file $get_img";
         exec($cmd);

     }


?>